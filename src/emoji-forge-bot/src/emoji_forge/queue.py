"""Job queue: one worker thread, persisted state, honest resume.

A long job never blocks polling: polling only enqueues. On restart,
jobs left in 'running' are marked 'interrupted' (never silently 'done');
the user can re-run them with /process.
"""
import queue
import threading
import time

from .storage import load_jobs, save_jobs


class JobQueue:
    def __init__(self, data_dir: str, worker_fn, max_workers: int = 1):
        self.data_dir = data_dir
        self.worker_fn = worker_fn
        self._queue: queue.Queue = queue.Queue()
        self._cancelled: set = set()
        self._thread = threading.Thread(target=self._run, daemon=True)
        self._running = False
        self.mark_interrupted_on_startup()

    def mark_interrupted_on_startup(self):
        jobs = load_jobs(self.data_dir)
        changed = False
        for job in jobs.get("jobs", []):
            if job.get("status") == "running":
                job["status"] = "interrupted"
                job["detail"] = "ربات ری‌استارت شد؛ کار نیمه‌تمام ماند و باید دوباره اجرا شود."
                changed = True
        if changed:
            save_jobs(self.data_dir, jobs)

    def start(self):
        if not self._running:
            self._running = True
            self._thread.start()

    def enqueue(self, job: dict) -> dict:
        jobs = load_jobs(self.data_dir)
        job = dict(job)
        job.setdefault("status", "queued")
        job.setdefault("created_at", time.time())
        jobs.setdefault("jobs", []).append(job)
        save_jobs(self.data_dir, jobs)
        self._queue.put(job)
        return job

    def cancel(self, job_id: str) -> None:
        """Mark a queued job cancelled. A running job checks this flag in
        the worker and stops before heavy work when it can."""
        self._cancelled.add(job_id)
        jobs = load_jobs(self.data_dir)
        for job in jobs.get("jobs", []):
            if job.get("id") == job_id and job.get("status") == "queued":
                job["status"] = "cancelled"
        save_jobs(self.data_dir, jobs)

    def is_cancelled(self, job_id: str) -> bool:
        return job_id in self._cancelled

    def _update(self, job_id: str, status: str, detail: str = ""):
        jobs = load_jobs(self.data_dir)
        for job in jobs.get("jobs", []):
            if job.get("id") == job_id:
                job["status"] = status
                if detail:
                    job["detail"] = detail
        save_jobs(self.data_dir, jobs)

    def _run(self):
        while True:
            job = self._queue.get()
            try:
                if job["id"] in self._cancelled:
                    self._update(job["id"], "cancelled", "کار توسط کاربر لغو شد.")
                    continue
                self._update(job["id"], "running")
                result = self.worker_fn(job)
                self._update(job["id"], "done", str(result)[:500])
            except Exception as exc:  # worker must never die silently
                self._update(job["id"], "failed", f"{type(exc).__name__}: {exc}")
            finally:
                self._queue.task_done()
