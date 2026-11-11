-- ============================================================
-- ToDoList Mini App — Cloudflare D1 schema
-- Apply once after creating the database:
--   wrangler d1 execute todolist --remote --file schema.sql
-- (columns reconstructed from the queries in worker.js)
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    telegram_id      INTEGER NOT NULL UNIQUE,
    first_name       TEXT    DEFAULT '',
    username         TEXT    DEFAULT '',
    language_code    TEXT    DEFAULT 'fa',
    theme            TEXT    DEFAULT 'dark',
    timezone         TEXT    DEFAULT 'Asia/Tehran',
    pin_hash         TEXT,
    pin_salt         TEXT,
    pin_fail_count   INTEGER DEFAULT 0,
    pin_locked_until INTEGER DEFAULT 0,
    created_at       INTEGER DEFAULT 0,
    updated_at       INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS categories (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    name       TEXT    NOT NULL,
    icon       TEXT    DEFAULT '',
    color      TEXT    DEFAULT '#6C5CE7',
    is_default INTEGER DEFAULT 0,
    created_at INTEGER DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_categories_user ON categories(user_id);

CREATE TABLE IF NOT EXISTS tasks (
    id                      INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id                 INTEGER NOT NULL,
    category_id             INTEGER,
    template_id             INTEGER,
    title                   TEXT    NOT NULL,
    description             TEXT    DEFAULT '',
    task_date               TEXT    DEFAULT '',
    task_time               TEXT    DEFAULT '',
    priority                TEXT    DEFAULT 'normal',
    color                   TEXT    DEFAULT '',
    icon                    TEXT    DEFAULT '',
    recurrence_type         TEXT    DEFAULT '',
    recurrence_days         TEXT    DEFAULT '[]',
    reminder_offset_minutes INTEGER,
    is_completed            INTEGER DEFAULT 0,
    completed_at            INTEGER,
    deleted_at              INTEGER,
    created_at              INTEGER DEFAULT 0,
    updated_at              INTEGER DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_tasks_user_date ON tasks(user_id, task_date);
CREATE INDEX IF NOT EXISTS idx_tasks_template  ON tasks(template_id);

CREATE TABLE IF NOT EXISTS reminders (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    task_id        INTEGER NOT NULL,
    user_id        INTEGER NOT NULL,
    offset_minutes INTEGER DEFAULT 0,
    repeat_type    TEXT    DEFAULT 'none',
    is_active      INTEGER DEFAULT 1,
    trigger_at     INTEGER DEFAULT 0,
    last_sent_at   INTEGER,
    created_at     INTEGER DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_reminders_trigger ON reminders(is_active, trigger_at);

CREATE TABLE IF NOT EXISTS habits (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    title      TEXT    NOT NULL,
    description TEXT   DEFAULT '',
    icon       TEXT    DEFAULT '',
    color      TEXT    DEFAULT '',
    daily_goal INTEGER DEFAULT 1,
    start_date TEXT    DEFAULT '',
    is_active  INTEGER DEFAULT 1,
    deleted_at INTEGER,
    created_at INTEGER DEFAULT 0,
    updated_at INTEGER DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_habits_user ON habits(user_id);

CREATE TABLE IF NOT EXISTS habit_logs (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    habit_id   INTEGER NOT NULL,
    log_date   TEXT    NOT NULL,
    count      INTEGER DEFAULT 1,
    created_at INTEGER DEFAULT 0,
    UNIQUE(habit_id, log_date)
);

CREATE TABLE IF NOT EXISTS badges (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    habit_id   INTEGER NOT NULL,
    badge_type TEXT    NOT NULL,
    earned_at  INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS notes (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    category_id INTEGER,
    title      TEXT    DEFAULT '',
    content    TEXT    DEFAULT '',
    color      TEXT    DEFAULT '',
    deleted_at INTEGER,
    created_at INTEGER DEFAULT 0,
    updated_at INTEGER DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_notes_user ON notes(user_id);

CREATE TABLE IF NOT EXISTS note_attachments (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    note_id             INTEGER NOT NULL,
    user_id             INTEGER NOT NULL,
    telegram_file_id    TEXT    DEFAULT '',
    telegram_message_id INTEGER,
    filename            TEXT    DEFAULT '',
    mime_type           TEXT    DEFAULT '',
    size                INTEGER DEFAULT 0,
    created_at          INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS ai_messages (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    role       TEXT    NOT NULL,
    content    TEXT    DEFAULT '',
    created_at INTEGER DEFAULT 0
);
