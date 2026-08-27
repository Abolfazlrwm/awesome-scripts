"""
System Monitor
--------------
A small PyQt6 desktop app with two tabs:
  1. System Monitor - live CPU / RAM / Disk usage, refreshed every second.
  2. System Info     - static info about the machine (OS, CPU, RAM, etc).

Run:
    python system_monitor.py
"""

import os
import platform
import sys

import psutil
from PyQt6.QtCore import Qt, QTimer
from PyQt6.QtGui import QFont
from PyQt6.QtWidgets import QApplication, QLabel, QTabWidget, QVBoxLayout, QWidget

# Root path used for disk usage - works on both Windows ("C:\\") and
# POSIX systems ("/").
DISK_ROOT = os.path.abspath(os.sep)


def _centered_label(text: str, font: QFont) -> QLabel:
    label = QLabel(text)
    label.setFont(font)
    label.setAlignment(Qt.AlignmentFlag.AlignCenter)
    return label


class SystemMonitorWindow(QWidget):
    def __init__(self) -> None:
        super().__init__()
        self.setWindowTitle("System Monitor")
        self.setFixedSize(500, 500)

        layout = QVBoxLayout()
        tabs = QTabWidget()

        tabs.addTab(self._build_monitor_tab(), "System Monitor")
        tabs.addTab(self._build_info_tab(), "System Info")

        layout.addWidget(tabs)
        self.setLayout(layout)

        # Live-update timer
        self.timer = QTimer()
        self.timer.timeout.connect(self.update_monitor)
        self.timer.start(1000)

    def _build_monitor_tab(self) -> QWidget:
        tab = QWidget()
        tab_layout = QVBoxLayout()
        font_monitor = QFont("Segoe UI", 16)

        self.cpu_usage_label = _centered_label("CPU Usage: ...%", font_monitor)
        self.ram_usage_label = _centered_label("RAM Usage: ...%", font_monitor)
        self.disk_usage_label = _centered_label("Disk Usage: ...%", font_monitor)

        tab_layout.addWidget(self.cpu_usage_label)
        tab_layout.addWidget(self.ram_usage_label)
        tab_layout.addWidget(self.disk_usage_label)
        tab_layout.addStretch()
        tab.setLayout(tab_layout)
        return tab

    def _build_info_tab(self) -> QWidget:
        tab = QWidget()
        tab_layout = QVBoxLayout()

        font_title = QFont("Segoe UI", 20)
        font_info = QFont("Segoe UI", 10)

        memory = psutil.virtual_memory()
        ram_gb = memory.total / (1024 ** 3)

        rows = [
            (f"{platform.system()}, {platform.release()}", font_title),
            (f"Processor: {platform.processor()}", font_info),
            (f"Machine: {platform.machine()}", font_info),
            (f"Hostname: {platform.node()}", font_info),
            (f"Architecture: {platform.architecture()[0]}", font_info),
            (f"RAM: {ram_gb:.2f} GB", font_info),
        ]
        for text, font in rows:
            tab_layout.addWidget(_centered_label(text, font))
        tab_layout.addStretch()

        tab.setLayout(tab_layout)
        return tab

    def update_monitor(self) -> None:
        try:
            cpu = psutil.cpu_percent()
            ram = psutil.virtual_memory().percent
            disk = psutil.disk_usage(DISK_ROOT).percent
        except OSError:
            return

        self.cpu_usage_label.setText(f"CPU Usage: {cpu}%")
        self.ram_usage_label.setText(f"RAM Usage: {ram}%")
        self.disk_usage_label.setText(f"Disk Usage: {disk}%")


def main() -> int:
    app = QApplication(sys.argv)
    window = SystemMonitorWindow()
    window.show()
    return app.exec()


if __name__ == "__main__":
    sys.exit(main())
