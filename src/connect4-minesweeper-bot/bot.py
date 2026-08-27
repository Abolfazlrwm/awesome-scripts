import logging
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup, InlineQueryResultArticle, InputTextMessageContent
from telegram.ext import Application, CommandHandler, CallbackQueryHandler, ContextTypes, ChatMemberHandler, MessageHandler, filters, InlineQueryHandler
import sqlite3
from datetime import datetime
import asyncio
import random

logging.basicConfig(format='%(asctime)s - %(name)s - %(levelname)s - %(message)s', level=logging.INFO)
logger = logging.getLogger(__name__)


TOKEN = "00000" #توکن ربات
ADMIN_ID = 123456789  # آیدی عددی ادمین را اینجا وارد کنید

def load_channels():
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('CREATE TABLE IF NOT EXISTS channels (id INTEGER PRIMARY KEY, username TEXT, name TEXT)')
    conn.commit()
    c.execute('SELECT username, name FROM channels ORDER BY id ASC')
    rows = c.fetchall()
    conn.close()
    return [{"username": r[0], "name": r[1]} for r in rows]

CHANNELS = load_channels()


MS_WIDTH = 7
MS_HEIGHT = 7
MS_MINES = 10

MS_CLOSED = "▫️"
MS_EMPTY = "⬜"
MS_FLAG = "🚩"
MS_MINE = "💣"
MS_EXPLODED = "💥"
MS_NUMBERS = ["", "1️⃣", "2️⃣", "3️⃣", "4️⃣", "5️⃣", "7️⃣", "8️⃣", "9️⃣"]


PLAYER1_SYMBOL = "🔵"
PLAYER2_SYMBOL = "🔴"
EMPTY_CELL = "⚪"
CF_WIDTH = 7
CF_HEIGHT = 6

games = {}
minesweeper_games = {}


def init_db():
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('''CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY,
        username TEXT,
        first_name TEXT,
        total_wins INTEGER DEFAULT 0,
        total_losses INTEGER DEFAULT 0,
        total_draws INTEGER DEFAULT 0,
        total_score INTEGER DEFAULT 0,
        current_win_streak INTEGER DEFAULT 0,
        max_win_streak INTEGER DEFAULT 0,
        first_game_date TEXT,
        last_game_date TEXT
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS group_scores (
        user_id INTEGER,
        chat_id INTEGER,
        wins INTEGER DEFAULT 0,
        losses INTEGER DEFAULT 0,
        draws INTEGER DEFAULT 0,
        score INTEGER DEFAULT 0,
        current_win_streak INTEGER DEFAULT 0,
        max_win_streak INTEGER DEFAULT 0,
        PRIMARY KEY (user_id, chat_id)
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS groups (
        chat_id INTEGER PRIMARY KEY,
        title TEXT,
        created_at TEXT
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS allowed_users (
        user_id INTEGER,
        chat_id INTEGER,
        granted_by INTEGER,
        granted_at TEXT,
        PRIMARY KEY (user_id, chat_id)
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS started_users (
        user_id INTEGER PRIMARY KEY,
        first_name TEXT,
        username TEXT,
        started_at TEXT
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS user_weekly_wins (
        user_id INTEGER PRIMARY KEY,
        first_name TEXT,
        username TEXT,
        win_count INTEGER DEFAULT 0,
        last_updated TEXT
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS user_weekly_wins_group (
        user_id INTEGER,
        chat_id INTEGER,
        first_name TEXT,
        username TEXT,
        win_count INTEGER DEFAULT 0,
        last_updated TEXT,
        PRIMARY KEY (user_id, chat_id)
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS channels (
        id INTEGER PRIMARY KEY,
        username TEXT,
        name TEXT
    )''')
    conn.commit()
    conn.close()

def register_user(user_id, first_name, username=None):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT total_wins FROM users WHERE user_id = ?', (user_id,))
    if c.fetchone() is None:
        now = datetime.now().isoformat()
        c.execute('''INSERT INTO users 
                     (user_id, first_name, username, total_wins, total_losses, total_draws, total_score)
                     VALUES (?, ?, ?, 0, 0, 0, 0)''',
                  (user_id, first_name, username))
    else:
        c.execute('UPDATE users SET first_name = ?, username = ? WHERE user_id = ?', (first_name, username, user_id))
    conn.commit()
    conn.close()

def register_started_user(user_id, first_name, username=None):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('INSERT OR IGNORE INTO started_users (user_id, first_name, username, started_at) VALUES (?, ?, ?, ?)',
              (user_id, first_name, username, datetime.now().isoformat()))
    conn.commit()
    conn.close()

def register_group(chat_id, title):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('INSERT OR IGNORE INTO groups (chat_id, title, created_at) VALUES (?, ?, ?)',
              (chat_id, title, datetime.now().isoformat()))
    conn.commit()
    conn.close()

def update_scores(user_id, chat_id, win=False, draw=False):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    now = datetime.now().isoformat()

    if win:
        c.execute('UPDATE users SET total_wins = total_wins + 1, total_score = total_score + 3 WHERE user_id = ?', (user_id,))
    elif draw:
        c.execute('UPDATE users SET total_draws = total_draws + 1, total_score = total_score + 1 WHERE user_id = ?', (user_id,))
    else:
        c.execute('UPDATE users SET total_losses = total_losses + 1 WHERE user_id = ?', (user_id,))

    c.execute('UPDATE users SET first_game_date = COALESCE(first_game_date, ?) WHERE user_id = ?', (now, user_id))
    c.execute('UPDATE users SET last_game_date = ? WHERE user_id = ?', (now, user_id))

    c.execute('INSERT OR IGNORE INTO group_scores (user_id, chat_id, wins, losses, draws, score) VALUES (?, ?, 0, 0, 0, 0)', (user_id, chat_id))
    if win:
        c.execute('UPDATE group_scores SET wins = wins + 1, score = score + 3 WHERE user_id = ? AND chat_id = ?', (user_id, chat_id))
    elif draw:
        c.execute('UPDATE group_scores SET draws = draws + 1, score = score + 1 WHERE user_id = ? AND chat_id = ?', (user_id, chat_id))
    else:
        c.execute('UPDATE group_scores SET losses = losses + 1 WHERE user_id = ? AND chat_id = ?', (user_id, chat_id))

    conn.commit()
    conn.close()

def get_group_leaderboard(chat_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('''SELECT u.first_name, gs.score FROM group_scores gs
                 JOIN users u ON gs.user_id = u.user_id
                 WHERE gs.chat_id = ?
                 ORDER BY gs.score DESC LIMIT 50''', (chat_id,))
    rows = c.fetchall()
    conn.close()
    return rows

def get_global_leaderboard():
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT first_name, total_score FROM users ORDER BY total_score DESC LIMIT 50')
    rows = c.fetchall()
    conn.close()
    return rows

def get_user_global_rank(user_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT total_score FROM users WHERE user_id = ?', (user_id,))
    row = c.fetchone()
    if not row:
        return 999
    score = row[0]
    c.execute('SELECT COUNT(*) FROM users WHERE total_score > ?', (score,))
    rank = c.fetchone()[0] + 1
    conn.close()
    return rank

def get_bot_stats():
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT COUNT(*) FROM started_users')
    started_count = c.fetchone()[0]
    c.execute('SELECT COUNT(*) FROM users')
    players_count = c.fetchone()[0]
    c.execute('SELECT COUNT(*) FROM groups')
    groups_count = c.fetchone()[0]

    active_games = sum(1 for g in games.values() if g.get('status') == 'playing')
    active_minesweeper_games = sum(1 for g in minesweeper_games.values() if g.get('status') == 'playing')
    total_active_games = active_games + active_minesweeper_games

    c.execute('SELECT SUM(total_score) FROM users')
    total_score_sum = c.fetchone()[0] or 0

    conn.close()
    return {
        'started_users': started_count,
        'active_players': players_count,
        'groups': groups_count,
        'active_games': total_active_games,
        'total_score': total_score_sum
    }

def get_user_medal(user_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('''
        SELECT user_id FROM users 
        ORDER BY total_score DESC 
        LIMIT 3
    ''')
    rows = c.fetchall()
    conn.close()

    if not rows:
        return ""

    if user_id == rows[0][0]:
        return "💎"
    elif user_id == rows[1][0]:
        return "🥇"
    elif user_id == rows[2][0]:
        return "🥈"
    else:
        return ""

def get_weekly_top_10():
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('''
        SELECT user_id, first_name, total_score 
        FROM users 
        ORDER BY total_score DESC 
        LIMIT 10
    ''')
    rows = c.fetchall()
    conn.close()
    return rows

def award_weekly_crowns():
    top_10 = get_weekly_top_10()
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    now = datetime.now().isoformat()

    for user_id, first_name, score in top_10:
        c.execute('''
            INSERT INTO user_weekly_wins (user_id, first_name, username, win_count, last_updated)
            VALUES (?, ?, ?, 1, ?)
            ON CONFLICT(user_id) DO UPDATE SET
            win_count = win_count + 1,
            first_name = excluded.first_name,
            last_updated = ?
        ''', (user_id, first_name, None, now, now))

    conn.commit()
    conn.close()

def get_user_crowns(user_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT win_count FROM user_weekly_wins WHERE user_id = ?', (user_id,))
    row = c.fetchone()
    conn.close()
    return row[0] if row else 0

async def post_weekly_results(context: ContextTypes.DEFAULT_TYPE):
    top_10 = get_weekly_top_10()
    if not top_10:
        return

    message = "برندگان هفتگی:\n\n"

    for i, (user_id, first_name, score) in enumerate(top_10, 1):
        conn = sqlite3.connect('game_bot.db')
        c = conn.cursor()
        c.execute('SELECT username FROM users WHERE user_id = ?', (user_id,))
        row = c.fetchone()
        conn.close()

        username = row[0] if row and row[0] else None
        crown_count = get_user_crowns(user_id)
        crowns = f" x{crown_count}" if crown_count > 1 else (" 👑" if crown_count == 1 else "")
        display_name = format_name_for_display(first_name)

        message += f"{i}. {display_name}{crowns} - {score} امتیاز\n"

    message += "\nتبریک به برنده‌ها!"
    message += "@YourBotUsername"

    try:
        await context.bot.send_message(
            chat_id="@YourChannelUsername",
            text=message,
            parse_mode='Markdown'
        )
    except Exception as e:
        logger.error(f"Error posting weekly results to @YourChannelUsername: {e}")

def get_group_top_10(chat_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('''
        SELECT u.user_id, u.first_name, gs.score 
        FROM group_scores gs
        JOIN users u ON gs.user_id = u.user_id
        WHERE gs.chat_id = ?
        ORDER BY gs.score DESC 
        LIMIT 10
    ''', (chat_id,))
    rows = c.fetchall()
    conn.close()
    return rows

def award_weekly_crowns_group(chat_id):
    top_10 = get_group_top_10(chat_id)
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    now = datetime.now().isoformat()

    for user_id, first_name, score in top_10:
        c.execute('''
            INSERT INTO user_weekly_wins_group (user_id, chat_id, first_name, username, win_count, last_updated)
            VALUES (?, ?, ?, ?, 1, ?)
            ON CONFLICT(user_id, chat_id) DO UPDATE SET
            win_count = win_count + 1,
            first_name = excluded.first_name,
            last_updated = ?
        ''', (user_id, chat_id, first_name, None, now, now))

    conn.commit()
    conn.close()

def get_user_crowns_group(user_id, chat_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT win_count FROM user_weekly_wins_group WHERE user_id = ? AND chat_id = ?', (user_id, chat_id))
    row = c.fetchone()
    conn.close()
    return row[0] if row else 0

async def post_group_results(chat_id, context: ContextTypes.DEFAULT_TYPE):
    top_10 = get_group_top_10(chat_id)
    if not top_10:
        return

    try:
        chat = await context.bot.get_chat(chat_id)
        title = chat.title or "این گروه"
    except:
        return

    message = f"برندگان هفتگی گروه {title}:\n\n"

    for i, (user_id, first_name, score) in enumerate(top_10, 1):
        conn = sqlite3.connect('game_bot.db')
        c = conn.cursor()
        c.execute('SELECT username FROM users WHERE user_id = ?', (user_id,))
        row = c.fetchone()
        conn.close()

        username = row[0] if row and row[0] else None
        crown_count = get_user_crowns_group(user_id, chat_id)
        crowns = f" x{crown_count}" if crown_count > 1 else (" 👑" if crown_count == 1 else "")
        display_name = format_name_for_display(first_name)

        message += f"{i}. {display_name}{crowns} - {score} امتیاز\n"

    message += "\nتبریک به برنده‌ها!"
    message += f"@YourBotUsername"

    try:
        sent_message = await context.bot.send_message(
            chat_id=chat_id,
            text=message,
            parse_mode='Markdown'
        )

        try:
            await context.bot.pin_chat_message(chat_id=chat_id, message_id=sent_message.message_id)
        except Exception as pin_error:
            logger.warning(f"Cannot pin message in {chat_id}: {pin_error}")

    except Exception as e:
        logger.error(f"Error sending group results to {chat_id}: {e}")

async def end_week_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if user.id != ADMIN_ID:
        await update.message.reply_text("فقط ادمین")
        return

    award_weekly_crowns()
    await post_weekly_results(context)

    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT chat_id FROM groups')
    all_group_ids = [row[0] for row in c.fetchall()]
    conn.close()

    target_group_ids = [gid for gid in all_group_ids if str(gid).startswith("-100")]

    for chat_id in target_group_ids:
        try:
            award_weekly_crowns_group(chat_id)
            await post_group_results(chat_id, context)
            await asyncio.sleep(1.5)
        except Exception as e:
            logger.error(f"Error processing group {chat_id}: {e}")

    await update.message.reply_text("هفته تمام شد. جوایز توزیع شد.")

# --- توابع دوز ---
def create_board():
    return [[EMPTY_CELL for _ in range(CF_WIDTH)] for _ in range(CF_HEIGHT)]

def drop_piece(board, col, player):
    symbol = PLAYER1_SYMBOL if player == 1 else PLAYER2_SYMBOL
    for row in range(CF_HEIGHT - 1, -1, -1):
        if board[row][col] == EMPTY_CELL:
            board[row][col] = symbol
            return row
    return None

def is_valid_move(board, col):
    return board[0][col] == EMPTY_CELL

def check_winner(board):
    for row in range(CF_HEIGHT):
        for col in range(CF_WIDTH):
            if board[row][col] == EMPTY_CELL:
                continue
            symbol = board[row][col]
            if col <= CF_WIDTH - 4 and all(board[row][col + i] == symbol for i in range(4)):
                return symbol
            if row <= CF_HEIGHT - 4 and all(board[row + i][col] == symbol for i in range(4)):
                return symbol
            if row <= CF_HEIGHT - 4 and col <= CF_WIDTH - 4 and all(board[row + i][col + i] == symbol for i in range(4)):
                return symbol
            if row <= CF_HEIGHT - 4 and col >= 3 and all(board[row + i][col - i] == symbol for i in range(4)):
                return symbol
    return None

def is_full(board):
    return all(board[0][col] != EMPTY_CELL for col in range(CF_WIDTH))

def format_board(board):
    lines = ["".join(row) for row in board]
    lines.append("".join(f"{i+1}\u20E3" for i in range(CF_WIDTH)))
    return "\n".join(lines)

def build_keyboard(game_id, user_id=None):
    columns = []
    for col in range(CF_WIDTH):
        text = f"{col+1}\u20E3"
        callback_data = f"move:{game_id}:{col}"
        columns.append(InlineKeyboardButton(text, callback_data=callback_data))
    keyboard = [columns]
    keyboard.append([InlineKeyboardButton("انصراف", callback_data=f"resign:{game_id}")])

    if user_id in [games.get(game_id, {}).get('player1'), games.get(game_id, {}).get('player2')]:
        keyboard.append([
            InlineKeyboardButton("ارسال مجدد", callback_data=f"resend:{game_id}"),
            InlineKeyboardButton("بازی با دوستان", switch_inline_query="")
        ])

    channel_buttons = []
    for i, channel in enumerate(load_channels()):
        btn = InlineKeyboardButton(channel["name"], url=f"https://t.me/{channel['username'][1:]}")
        channel_buttons.append(btn)
        if (i + 1) % 2 == 0:
            keyboard.append(channel_buttons)
            channel_buttons = []
    if channel_buttons:
        keyboard.append(channel_buttons)

    return InlineKeyboardMarkup(keyboard)

def start_advanced_timer(game_id, context):
    game = games.get(game_id)
    if not game or game['status'] != 'playing':
        return

    if game.get('timer_task'):
        game['timer_task'].cancel()

    game['time_left'] = 60

    async def countdown():
        try:
            while game['status'] == 'playing' and game['time_left'] > 0:
                await asyncio.sleep(1)
                game['time_left'] -= 1

            if game['status'] == 'playing' and game['time_left'] <= 0:
                await handle_timeout(game_id, context)
        except asyncio.CancelledError:
            pass
        except Exception as e:
            logger.error(f"Critical error in advanced timer for game {game_id}: {e}")

    task = context.application.create_task(countdown())
    game['timer_task'] = task

async def handle_timeout(game_id, context):
    game = games.get(game_id)
    if not game or game['status'] != 'playing':
        return

    winner_id = game['player2'] if game['current_turn'] == 1 else game['player1']
    loser_id = game['player1'] if game['current_turn'] == 1 else game['player2']

    update_scores(winner_id, game['chat_id'], win=True)
    update_scores(loser_id, game['chat_id'], win=False)

    winner_user = await context.bot.get_chat(winner_id)
    loser_user = await context.bot.get_chat(loser_id)

    game['status'] = 'finished'
    game['timer_task'] = None

    text = "زمان تمام شد!\n\n"
    text += f"برنده: {winner_user.first_name}\n"
    text += f"بازنده: {loser_user.first_name}\n\n"
    text += format_board(game['board'])

    try:
        await context.bot.edit_message_text(
            chat_id=game['chat_id'],
            message_id=game['message_id'],
            text=text,
            parse_mode='HTML',
            reply_markup=None
        )
    except Exception as e:
        logger.error(f"Error editing message on timeout: {e}")

# --- توابع مین‌یاب ---
def ms_create_board():
    return [[MS_CLOSED for _ in range(MS_WIDTH)] for _ in range(MS_HEIGHT)]

def ms_place_mines(first_click_row, first_click_col):
    mines = set()
    while len(mines) < MS_MINES:
        row = random.randint(0, MS_HEIGHT - 1)
        col = random.randint(0, MS_WIDTH - 1)
        if abs(row - first_click_row) <= 1 and abs(col - first_click_col) <= 1:
            continue
        mines.add((row, col))
    return mines

def ms_count_adjacent_mines(board, mines, row, col):
    if (row, col) in mines:
        return -1
    count = 0
    for r in range(max(0, row-1), min(MS_HEIGHT, row+2)):
        for c in range(max(0, col-1), min(MS_WIDTH, col+2)):
            if (r, c) in mines:
                count += 1
    return count

def ms_reveal_cell(game, row, col):
    board = game['board']
    mines = game['mines']
    if board[row][col] != MS_CLOSED:
        return False, False

    is_mine = (row, col) in mines
    if is_mine:
        board[row][col] = MS_MINE
        return True, True
    else:
        count = ms_count_adjacent_mines(board, mines, row, col)
        board[row][col] = MS_NUMBERS[count] if count > 0 else MS_EMPTY
        return True, False

def ms_is_game_over(game):
    board = game['board']
    for row in range(MS_HEIGHT):
        for col in range(MS_WIDTH):
            if board[row][col] == MS_CLOSED:
                return False
    return True

def ms_build_keyboard(game_id, user_id=None):
    keyboard = []

    game = minesweeper_games.get(game_id)
    if not game:
        return InlineKeyboardMarkup([])

    if game['mines'] is None:
        mine_count = MS_MINES
    else:
        mine_count = sum(
            1 for (r, c) in game['mines']
            if game['board'][r][c] == MS_CLOSED
        )

    row = [
        InlineKeyboardButton(f"مین باقی‌مانده: {mine_count}", callback_data="dummy")
    ]
    keyboard.append(row)

    for row_idx in range(MS_HEIGHT):
        row_buttons = []
        for col_idx in range(MS_WIDTH):
            text = game['board'][row_idx][col_idx]
            callback_data = f"ms_move:{game_id}:{row_idx}:{col_idx}"
            row_buttons.append(InlineKeyboardButton(text, callback_data=callback_data))
        keyboard.append(row_buttons)

    p1_score = game['scores'].get(1, 0)
    p2_score = game['scores'].get(2, 0)

    try:
        p1 = game.get('player1_obj')
        p2 = game.get('player2_obj')
        if not p1 or not p2:
            raise Exception("Player objects not loaded")
        p1_name = p1.first_name[:10]
        p2_name = p2.first_name[:10]
    except:
        p1_name = "بازیکن ۱"
        p2_name = "بازیکن ۲"

    keyboard.append([
        InlineKeyboardButton(f"{p1_name}: {p1_score} مین", callback_data="dummy"),
        InlineKeyboardButton(f"{p2_name}: {p2_score} مین", callback_data="dummy")
    ])

    keyboard.append([
        InlineKeyboardButton("انصراف", callback_data=f"ms_resign:{game_id}")
    ])

    if user_id in [game.get('player1'), game.get('player2')]:
        keyboard.append([
            InlineKeyboardButton("ارسال مجدد", callback_data=f"ms_resend:{game_id}"),
            InlineKeyboardButton("بازی با دوستان", switch_inline_query="")
        ])

    channel_buttons = []
    for i, channel in enumerate(load_channels()):
        btn = InlineKeyboardButton(channel["name"], url=f"https://t.me/{channel['username'][1:]}")
        channel_buttons.append(btn)
        if (i + 1) % 2 == 0:
            keyboard.append(channel_buttons)
            channel_buttons = []
    if channel_buttons:
        keyboard.append(channel_buttons)

    return InlineKeyboardMarkup(keyboard)

# --- شروع مین‌یاب ---
async def start_minesweeper_game(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_permission(update, context):
        return

    user = update.effective_user
    chat = update.effective_chat

    if chat.type == "private":
        await update.message.reply_text("این بازی فقط در گروه است.")
        return

    if not await is_bot_admin(context.bot, chat.id):
        await update.message.reply_text("ربات باید ادمین باشد.")
        return

    game_id = f"ms_{chat.id}"
    if game_id in minesweeper_games and minesweeper_games[game_id]['status'] == 'playing':
        await update.message.reply_text("بازی مین‌یاب در حال انجام است!")
        return

    minesweeper_games[game_id] = {
        'board': ms_create_board(),
        'mines': set(),
        'player1': user.id,
        'player2': None,
        'current_turn': 1,
        'status': 'waiting',
        'chat_id': chat.id,
        'timer_task': None,
        'time_left': 60,
        'message_id': None,
        'first_click': None,
        'scores': {1: 0, 2: 0},
    }

    register_user(user.id, user.first_name, user.username)
    register_group(chat.id, chat.title or "گروه ناشناس")

    channel_list_text = "\n".join([f"- {ch['name']} (@{ch['username'][1:]})" for ch in load_channels()])

    keyboard = InlineKeyboardMarkup([[
        InlineKeyboardButton("پیوستن به مین‌یاب", callback_data=f"ms_join:{game_id}")
    ]])

    msg = await update.message.reply_text(
        f"{user.first_name} شما رو به مین‌یاب دعوت کرد!\n\n"
        f"قوانین:\n"
        f"- هرکس مین بیشتری پیدا کنه برنده است\n"
        f"- اگر مین بزنی +1 امتیاز و یه نوبت دیگه\n"
        f"- اگر مین نبود عدد مین‌های اطراف رو نشون میده\n\n"
        f"اول عضو کانال‌ها شو:\n{channel_list_text}",
        reply_markup=keyboard
    )

    try:
        await context.bot.pin_chat_message(chat_id=chat.id, message_id=msg.message_id)
    except:
        pass

async def ms_join_game(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    parts = query.data.split(":")
    game_id = parts[1]
    user = query.from_user

    if game_id not in minesweeper_games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = minesweeper_games[game_id]
    if game['player2'] is not None:
        await query.answer("بازیکن دوم قبلاً پیوسته.", show_alert=True)
        return

    if user.id == game['player1']:
        await query.answer("با خودت بازی نکن!", show_alert=True)
        return

    register_user(user.id, user.first_name, user.username)
    if 'chat_id' in game:
        register_group(game['chat_id'], (await context.bot.get_chat(game['chat_id'])).title)

    game['player2'] = user.id
    game['status'] = 'playing'
    game['current_turn'] = 1

    p1 = await context.bot.get_chat(game['player1'])
    p2 = user
    game['player1_obj'] = p1
    game['player2_obj'] = p2

    text = f"مین‌یاب: {p1.first_name} vs {p2.first_name}\n\n"
    text += "بازی شروع شد!\n"
    text += f"مین باقی‌مانده: {MS_MINES}\n\n"
    text += f"نوبت: {p1.first_name}"

    keyboard = ms_build_keyboard(game_id, user.id)
    await query.edit_message_text(text=text, reply_markup=keyboard)
    game['message_id'] = query.message.message_id
    start_ms_timer(game_id, context)

# --- تایمر مین‌یاب ---
def start_ms_timer(game_id, context):
    game = minesweeper_games.get(game_id)
    if not game or game['status'] != 'playing':
        return

    if game.get('timer_task'):
        game['timer_task'].cancel()

    game['time_left'] = 60

    async def countdown():
        try:
            while game['status'] == 'playing' and game['time_left'] > 0:
                await asyncio.sleep(1)
                game['time_left'] -= 1

            if game['status'] == 'playing' and game['time_left'] <= 0:
                await ms_handle_timeout(game_id, context)
        except asyncio.CancelledError:
            pass
        except Exception as e:
            logger.error(f"Timer error in minesweeper {game_id}: {e}")

    task = context.application.create_task(countdown())
    game['timer_task'] = task

async def ms_handle_timeout(game_id, context: ContextTypes.DEFAULT_TYPE):
    game = minesweeper_games.get(game_id)
    if not game or game['status'] != 'playing':
        return

    next_turn = 2 if game['current_turn'] == 1 else 1
    game['current_turn'] = next_turn

    p1 = await context.bot.get_chat(game['player1'])
    p2 = await context.bot.get_chat(game['player2'])
    loser_name = p1.first_name if next_turn == 2 else p2.first_name
    next_player_name = p2.first_name if next_turn == 2 else p1.first_name

    text = f"زمان {loser_name} تمام شد!\n"
    text += f"نوبت: {next_player_name}\n\n"
    text += f"مین باقی‌مانده: {sum(1 for (r,c) in game['mines'] if game['board'][r][c] == MS_CLOSED)}\n\n"

    try:
        await context.bot.edit_message_text(
            chat_id=game['chat_id'],
            message_id=game['message_id'],
            text=text,
            parse_mode='HTML',
            reply_markup=ms_build_keyboard(game_id)
        )
    except Exception as e:
        logger.error(f"Error editing message on timeout: {e}")

    start_ms_timer(game_id, context)

# --- حرکت مین‌یاب ---
async def ms_handle_move(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data.split(":")
    if data[0] != "ms_move": return

    game_id, row, col = data[1], int(data[2]), int(data[3])
    user_id = query.from_user.id

    if game_id not in minesweeper_games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = minesweeper_games[game_id]
    if game['status'] != 'playing':
        await query.answer("بازی تمام شد.", show_alert=True)
        return

    if user_id not in [game['player1'], game['player2']]:
        await query.answer("شما بازیکن نیستید!", show_alert=True)
        return

    player_num = 1 if user_id == game['player1'] else 2
    if player_num != game['current_turn']:
        await query.answer("نوبت شما نیست!", show_alert=True)
        return

    if not await check_subscription(user_id, context.bot):
        channel_list = "\n".join([f"- {ch['name']} (@{ch['username'][1:]})" for ch in load_channels()])
        await query.answer(
            f"عضو کانال‌ها شو:\n{channel_list}",
            show_alert=True
        )
        return

    if game['first_click'] is None:
        game['first_click'] = (row, col)
        game['mines'] = ms_place_mines(row, col)

    revealed, is_mine = ms_reveal_cell(game, row, col)

    if not revealed:
        await query.answer("قبلاً باز شده!", show_alert=True)
        return

    if is_mine:
        game['scores'][player_num] += 1

        text = f"{game['player1_obj'].first_name} vs {game['player2_obj'].first_name}\n\n"
        text += f"{query.from_user.first_name} مین پیدا کرد! +1\n"
        text += "یه نوبت دیگه!\n\n"
        text += f"مین باقی‌مانده: {sum(1 for (r,c) in game['mines'] if game['board'][r][c] == MS_CLOSED)}\n\n"

    else:
        game['current_turn'] = 2 if player_num == 1 else 1

        p1 = game['player1_obj']
        p2 = game['player2_obj']
        next_player = p2 if player_num == 1 else p1

        text = f"{p1.first_name} vs {p2.first_name}\n\n"
        text += f"{query.from_user.first_name} روی خونه امن کلیک کرد.\n"
        text += f"نوبت: {next_player.first_name}\n\n"
        text += f"مین باقی‌مانده: {sum(1 for (r,c) in game['mines'] if game['board'][r][c] == MS_CLOSED)}\n\n"

    if ms_is_game_over(game):
        game['status'] = 'finished'
        if game.get('timer_task'):
            game['timer_task'].cancel()
            game['timer_task'] = None

        p1_score = game['scores'][1]
        p2_score = game['scores'][2]

        if p1_score > p2_score:
            winner_id = game['player1']
            winner_name = (await context.bot.get_chat(winner_id)).first_name
        elif p2_score > p1_score:
            winner_id = game['player2']
            winner_name = (await context.bot.get_chat(winner_id)).first_name
        else:
            winner_id = None
            winner_name = "مساوی"

        if winner_id:
            update_scores(winner_id, game['chat_id'], win=True)
            loser_id = game['player2'] if winner_id == game['player1'] else game['player2']
            update_scores(loser_id, game['chat_id'], win=False)
        else:
            update_scores(game['player1'], game['chat_id'], draw=True)
            update_scores(game['player2'], game['chat_id'], draw=True)

        text = f"بازی تمام شد!\n\n"
        text += f"امتیاز نهایی:\n"
        text += f"{game['player1_obj'].first_name}: {p1_score} مین\n"
        text += f"{game['player2_obj'].first_name}: {p2_score} مین\n\n"
        if winner_id:
            text += f"برنده: {winner_name}\n"
        else:
            text += f"مساوی!\n"

    await query.edit_message_text(text=text, reply_markup=ms_build_keyboard(game_id, user_id))
    await query.answer()

    if game['status'] == 'playing' and not is_mine:
        start_ms_timer(game_id, context)

# --- انصراف مین‌یاب ---
async def ms_resign(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    game_id = query.data.split(":")[1]
    user_id = query.from_user.id

    if game_id not in minesweeper_games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = minesweeper_games[game_id]
    if user_id not in [game['player1'], game['player2']]:
        await query.answer("فقط بازیکنان می‌تونن انصراف بدن.", show_alert=True)
        return

    opponent_id = game['player2'] if user_id == game['player1'] else game['player2']
    update_scores(opponent_id, game['chat_id'], win=True)
    update_scores(user_id, game['chat_id'], win=False)

    if game.get('timer_task'):
        game['timer_task'].cancel()
        game['timer_task'] = None

    opponent = await context.bot.get_chat(opponent_id)
    text = "بازی تمام شد!\n\n"
    text += f"برنده: {opponent.first_name}\n"
    text += f"انصراف: {query.from_user.first_name}\n\n"

    game['status'] = 'finished'
    await query.edit_message_text(text=text, parse_mode='HTML', reply_markup=None)
    await query.answer()

# --- ارسال مجدد مین‌یاب ---
async def ms_resend_game(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    game_id = query.data.split(":")[1]
    user_id = query.from_user.id

    if game_id not in minesweeper_games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = minesweeper_games[game_id]
    chat_id = game['chat_id']

    if user_id not in [game['player1'], game['player2']]:
        await query.answer("فقط بازیکنان می‌تونن ارسال کنن.", show_alert=True)
        return

    if not await is_bot_admin(context.bot, chat_id):
        await query.answer("ربات ادمین نیست.", show_alert=True)
        return

    if game['status'] != 'playing':
        await query.answer("بازی در جریان نیست.", show_alert=True)
        return

    try:
        await context.bot.unpin_chat_message(chat_id)
    except:
        pass

    p1 = await context.bot.get_chat(game['player1'])
    p2 = await context.bot.get_chat(game['player2'])
    current_turn_name = p1.first_name if game['current_turn'] == 1 else p2.first_name

    def format_name(name, user_id, chat_id):
        medal = get_user_medal(user_id)
        crowns_global = get_user_crowns(user_id)
        crowns_group = get_user_crowns_group(user_id, chat_id)
        parts = [name]
        if medal:
            parts.append(medal)
        parts.append("|")
        if crowns_global > 0:
            parts.append(f"👑{crowns_global}")
        if crowns_group > 0:
            parts.append(f" medals{crowns_group}")
        return " ".join(parts).strip()

    p1_display = format_name(p1.first_name, p1.id, chat_id)
    p2_display = format_name(p2.first_name, p2.id, chat_id)

    text = f"{p1_display} vs {p2_display}\n\n"
    text += f"نوبت: {current_turn_name}\n"
    text += f"مین باقی‌مانده: {sum(1 for (r,c) in game['mines'] if game['board'][r][c] == MS_CLOSED)}\n\n"

    try:
        msg = await context.bot.send_message(
            chat_id=chat_id,
            text=text,
            reply_markup=ms_build_keyboard(game_id, user_id)
        )
        await context.bot.pin_chat_message(chat_id=chat_id, message_id=msg.message_id)
        await context.bot.send_message(
            chat_id=chat.id,
            text="بازی مجدداً ارسال شد.",
            disable_notification=True
        )
        await query.answer("بازی ارسال شد.")
        start_ms_timer(game_id, context)
    except Exception as e:
        await query.answer(f"خطا: {str(e)}", show_alert=True)

# --- راهنمای مین‌یاب ---
async def ms_help_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_permission(update, context):
        return

    text = """
راهنمای مین‌یاب:

هدف: بیشتر از حریف مین پیدا کن!

نحوه بازی:
1. هر بازیکن به نوبت یه خونه رو کلیک می‌کنه
2. اگر مین باشه: +1 امتیاز و یه نوبت دیگه
3. اگر مین نباشه: عدد مین‌های اطراف رو نشون میده
4. بازی تا پیدا شدن همه مین‌ها ادامه داره
5. هرکس مین بیشتری پیدا کنه برنده است

نکته: از اعداد برای حدس زدن مین‌ها استفاده کن!
"""
    await update.message.reply_text(text, parse_mode='Markdown')

# --- توابع عمومی ---
async def is_admin(bot, chat_id, user_id):
    try:
        member = await bot.get_chat_member(chat_id, user_id)
        return member.status in ['administrator', 'creator']
    except:
        return False

async def check_permission(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    user = update.effective_user
    chat = update.effective_chat

    if chat.type == "private":
        return True

    if await is_admin(context.bot, chat.id, user.id):
        return True

    if is_user_allowed(user.id, chat.id):
        return True

    await update.message.reply_text(
        "شما دسترسی ندارید."
    )
    return False

async def check_subscription(user_id, bot):
    channels = load_channels()
    for channel in channels:
        try:
            member = await bot.get_chat_member(channel["username"], user_id)
            if member.status not in ['member', 'administrator', 'creator']:
                return False
        except:
            return False
    return True

def is_user_allowed(user_id, chat_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('SELECT 1 FROM allowed_users WHERE user_id = ? AND chat_id = ?', (user_id, chat_id))
    row = c.fetchone()
    conn.close()
    return row is not None

def grant_access(user_id, chat_id, admin_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('INSERT OR REPLACE INTO allowed_users (user_id, chat_id, granted_by, granted_at) VALUES (?, ?, ?, ?)',
              (user_id, chat_id, admin_id, datetime.now().isoformat()))
    conn.commit()
    conn.close()

def revoke_access(user_id, chat_id):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('DELETE FROM allowed_users WHERE user_id = ? AND chat_id = ?', (user_id, chat_id))
    conn.commit()
    conn.close()

async def is_bot_admin(bot, chat_id):
    try:
        member = await bot.get_chat_member(chat_id, bot.id)
        return member.status in ['administrator', 'creator']
    except:
        return False

def format_name_for_display(first_name):
    name = first_name.strip()
    return name.replace("[", "").replace("]", "").replace("`", "").replace("*", "").replace("_", "").replace(">", "").replace("<", "")

# --- پنل ادمین ---
async def admin_panel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if user.id != ADMIN_ID:
        await update.message.reply_text("دسترسی محدود.")
        return

    keyboard = [
        [
            InlineKeyboardButton("ارسال همگانی", callback_data="broadcast_menu"),
            InlineKeyboardButton("آمار", callback_data="admin_stats")
        ],
        [
            InlineKeyboardButton("افزودن کانال", callback_data="add_channel_step1"),
            InlineKeyboardButton("حذف کانال", callback_data="remove_channel_step1")
        ]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    await update.message.reply_text("پنل مدیریت", reply_markup=reply_markup)

# --- منوی ارسال همگانی ---
async def show_broadcast_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if query.from_user.id != ADMIN_ID:
        await query.answer("فقط ادمین.", show_alert=True)
        return

    keyboard = [
        [
            InlineKeyboardButton("ارسال پیوی", callback_data="broadcast_private_start"),
            InlineKeyboardButton("ارسال گروه", callback_data="broadcast_group_start")
        ],
        [
            InlineKeyboardButton("فوروارد پیوی", callback_data="forward_private_start"),
            InlineKeyboardButton("فوروارد گروه", callback_data="forward_group_start")
        ],
        [
            InlineKeyboardButton("بازگشت", callback_data="back_to_admin")
        ]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    await query.edit_message_text("نوع ارسال را انتخاب کن:", reply_markup=reply_markup)

# --- مدیریت کانال‌ها ---
async def add_channel_step1(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user = query.from_user
    if user.id != ADMIN_ID:
        await query.answer("فقط ادمین.", show_alert=True)
        return

    await query.answer()
    await query.edit_message_text("یوزرنیم و نام کانال رو بفرست:\n\n@username\nنام نمایشی")
    context.user_data['waiting_for_add_channel'] = True

async def receive_add_channel_data(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.user_data.get('waiting_for_add_channel'):
        return

    user = update.effective_user
    if user.id != ADMIN_ID:
        return

    text = update.message.text
    if not text:
        await update.message.reply_text("داده نامعتبر.")
        return

    try:
        username, name = text.split('\n', 1)
        username = username.strip()
        name = name.strip()
        if not username.startswith('@'):
            raise ValueError
    except ValueError:
        await update.message.reply_text("فرمت اشتباه. دوباره تلاش کن.\n\n@username\nنام نمایشی")
        return

    context.user_data['waiting_for_add_channel'] = False

    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('INSERT OR REPLACE INTO channels (username, name) VALUES (?, ?)', (username, name))
    conn.commit()
    conn.close()

    CHANNELS.clear()
    CHANNELS.extend(load_channels())

    await update.message.reply_text(f"کانال {name} اضافه شد.")

async def remove_channel_step1(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user = query.from_user
    if user.id != ADMIN_ID:
        await query.answer("فقط ادمین.", show_alert=True)
        return

    await query.answer()
    channels = load_channels()
    if not channels:
        await query.edit_message_text("کانالی برای حذف نیست.")
        return

    keyboard = []
    for ch in channels:
        keyboard.append([InlineKeyboardButton(ch["name"], callback_data=f"confirm_remove_channel:{ch['username']}")])
    keyboard.append([InlineKeyboardButton("بازگشت", callback_data="back_to_admin")])
    reply_markup = InlineKeyboardMarkup(keyboard)
    await query.edit_message_text("کانال رو انتخاب کن:", reply_markup=reply_markup)

async def confirm_remove_channel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user = query.from_user
    if user.id != ADMIN_ID:
        await query.answer("فقط ادمین.", show_alert=True)
        return

    username = query.data.split(":")[1]

    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('DELETE FROM channels WHERE username = ?', (username,))
    conn.commit()
    conn.close()

    CHANNELS.clear()
    CHANNELS.extend(load_channels())

    await query.edit_message_text(f"کانال {username} حذف شد.")
    await query.answer()

# --- توابع ارسال همگانی ---
async def start_broadcast_private(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text("پیامت رو بفرست:")
    context.user_data['expecting_broadcast'] = 'private'

async def start_broadcast_group(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text("پیامت رو بفرست:")
    context.user_data['expecting_broadcast'] = 'group'

async def start_forward_private(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text("پیام رو فوروارد کن:")
    context.user_data['expecting_broadcast'] = 'private_forward'

async def start_forward_group(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text("پیام رو فوروارد کن:")
    context.user_data['expecting_broadcast'] = 'group_forward'

async def receive_broadcast_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_data = context.user_data
    if 'expecting_broadcast' not in user_data:
        return

    if update.effective_user.id != ADMIN_ID:
        return

    mode = user_data['expecting_broadcast']
    del user_data['expecting_broadcast']

    if mode == 'private':
        await broadcast_message(context, update.message)
    elif mode == 'group':
        await broadcast_message(context, update.message, to_groups=True)
    elif mode == 'private_forward':
        await forward_message(context, update.message)
    elif mode == 'group_forward':
        await forward_message(context, update.message, to_groups=True)

async def broadcast_message(context: ContextTypes.DEFAULT_TYPE, message, to_groups=False):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    if to_groups:
        c.execute('SELECT chat_id FROM groups')
    else:
        c.execute('SELECT user_id FROM started_users')
    ids = [row[0] for row in c.fetchall()]
    conn.close()

    sent_count = 0
    failed_count = 0

    for chat_id in ids:
        try:
            await message.copy(chat_id=chat_id)
            sent_count += 1
        except Exception as e:
            logger.warning(f"Failed to send message to {chat_id}: {e}")
            failed_count += 1

        if sent_count % 100 == 0:
            logger.info(f"Sent {sent_count} messages, pausing for 60 seconds...")
            await asyncio.sleep(60)

    result_text = f"ارسال {'گروه' if to_groups else 'کاربران'} تمام شد.\n\n"
    result_text += f"ارسال: {sent_count}\n"
    result_text += f"خطا: {failed_count}"
    await context.bot.send_message(chat_id=ADMIN_ID, text=result_text)

async def forward_message(context: ContextTypes.DEFAULT_TYPE, message, to_groups=False):
    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    if to_groups:
        c.execute('SELECT chat_id FROM groups')
    else:
        c.execute('SELECT user_id FROM started_users')
    ids = [row[0] for row in c.fetchall()]
    conn.close()

    sent_count = 0
    failed_count = 0

    for chat_id in ids:
        try:
            await message.forward(chat_id=chat_id)
            sent_count += 1
        except Exception as e:
            logger.warning(f"Failed to forward message to {chat_id}: {e}")
            failed_count += 1

        if sent_count % 100 == 0:
            logger.info(f"Forwarded {sent_count} messages, pausing for 60 seconds...")
            await asyncio.sleep(60)

    result_text = f"فوروارد {'گروه' if to_groups else 'کاربران'} تمام شد.\n\n"
    result_text += f"فوروارد: {sent_count}\n"
    result_text += f"خطا: {failed_count}"
    await context.bot.send_message(chat_id=ADMIN_ID, text=result_text)

async def show_admin_stats(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if query.from_user.id != ADMIN_ID:
        await query.answer("فقط ادمین.", show_alert=True)
        return

    stats = get_bot_stats()
    text = "آمار ربات:\n\n"
    text += f"کاربران: {stats['started_users']}\n"
    text += f"بازیکنان: {stats['active_players']}\n"
    text += f"گروه‌ها: {stats['groups']}\n"
    text += f"بازی‌های جاری: {stats['active_games']}\n"
    text += f"امتیاز کل: {stats['total_score']}"

    keyboard = [[InlineKeyboardButton("بازگشت", callback_data="back_to_admin")]]
    await query.edit_message_text(text=text, parse_mode='HTML', reply_markup=InlineKeyboardMarkup(keyboard))

async def back_to_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await admin_panel(update, context)

# --- هندلرها ---
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat

    register_started_user(user.id, user.first_name, user.username)

    if chat.type == "private":
        keyboard = [
            [InlineKeyboardButton("بازی با دوستان", switch_inline_query="")],
            [InlineKeyboardButton("مین‌یاب", switch_inline_query="minesweeper")],
            [
                InlineKeyboardButton("۵۰ برتر", callback_data="global_lb"),
                InlineKeyboardButton("آمار من", callback_data="my_stats")
            ],
            [InlineKeyboardButton("افزودن به گروه", url="https://t.me/YourBotUsername?startgroup=start")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)

        text = f"سلام {user.first_name}!\n\n"
        text += "بازی کن، با دوستان رقابت کن و برنده شو!\n\n"
        text += "از دکمه‌های زیر استفاده کن."

        if update.callback_query:
            try:
                await update.callback_query.edit_message_text(text=text, reply_markup=reply_markup)
            except:
                await update.callback_query.message.reply_text(text, reply_markup=reply_markup)
        else:
            await update.message.reply_text(text, reply_markup=reply_markup)
    else:
        if await check_permission(update, context):
            await update.message.reply_text("سلام! از دستورات «دوز» یا «مین‌یاب» استفاده کن.")

async def start_game(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_permission(update, context):
        return

    user = update.effective_user
    chat = update.effective_chat

    if chat.type == "private":
        await update.message.reply_text("این بازی فقط در گروه است.")
        return

    if not await is_bot_admin(context.bot, chat.id):
        await update.message.reply_text("ربات باید ادمین گروه باشه.")
        return

    register_user(user.id, user.first_name, user.username)
    register_group(chat.id, chat.title or "گروه ناشناس")

    game_id = str(chat.id)
    if game_id in games and games[game_id]['status'] == 'playing':
        await update.message.reply_text("یک بازی در حال انجام است!")
        return

    games[game_id] = {
        'board': create_board(),
        'player1': user.id,
        'player2': None,
        'current_turn': 1,
        'status': 'waiting',
        'chat_id': chat.id,
        'timer_task': None,
        'time_left': 60,
        'message_id': None
    }

    p1_name = user.first_name

    channel_list_text = "\n".join([f"- {ch['name']} (@{ch['username'][1:]})" for ch in load_channels()])

    keyboard = InlineKeyboardMarkup([[
        InlineKeyboardButton("پیوستن به بازی", callback_data=f"join:{game_id}")
    ]])

    msg = await update.message.reply_text(
        f"{p1_name} شما رو به بازی دوز دعوت کرد!\n\n"
        f"اول عضو کانال‌ها شو:\n{channel_list_text}\n\n"
        f"روی دکمه زیر بزن تا بپیوندی.",
        reply_markup=keyboard
    )

    try:
        await context.bot.pin_chat_message(chat_id=chat.id, message_id=msg.message_id)
        await context.bot.send_message(
            chat_id=chat.id,
            text="بازی جدید! همه می‌تونن بپیوندن.",
            disable_notification=True
        )
    except:
        await update.message.reply_text("نتونستم پیام رو سنجاق کنم. لطفاً دستی سنجاق کن.")

async def join_game(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    game_id = query.data.split(":")[1]
    user = query.from_user

    if game_id not in games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = games[game_id]
    if game['player2'] is not None:
        await query.answer("بازیکن دوم قبلاً پیوسته.", show_alert=True)
        return

    if user.id == game['player1']:
        await query.answer("با خودت بازی نکن!", show_alert=True)
        return

    register_user(user.id, user.first_name, user.username)
    if 'chat_id' in game:
        register_group(game['chat_id'], (await context.bot.get_chat(game['chat_id'])).title)

    game['player2'] = user.id
    game['status'] = 'playing'
    game['current_turn'] = 1

    p1 = await context.bot.get_chat(game['player1'])
    p2 = user

    def format_name(name, user_id, chat_id):
        medal = get_user_medal(user_id)
        crowns_global = get_user_crowns(user_id)
        crowns_group = get_user_crowns_group(user_id, chat_id)
        parts = [name]
        if medal:
            parts.append(medal)
        parts.append("|")
        if crowns_global > 0:
            parts.append(f"👑{crowns_global}")
        if crowns_group > 0:
            parts.append(f" medals{crowns_group}")
        return " ".join(parts).strip()

    p1_display = format_name(p1.first_name, p1.id, game['chat_id'])
    p2_display = format_name(p2.first_name, p2.id, game['chat_id'])

    board = game['board']
    text = f"{p1_display} ({PLAYER1_SYMBOL})\nدر برابر\n{p2_display} ({PLAYER2_SYMBOL})\n\n"
    text += format_board(board)
    text += f"\n\nنوبت: {p1.first_name}"

    await query.edit_message_text(text=text, reply_markup=build_keyboard(game_id, user.id))
    game['message_id'] = query.message.message_id
    start_advanced_timer(game_id, context)

async def inline_query(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.inline_query
    user = query.from_user
    results = []

async def handle_inline(update, context):
    query = update.inline_query
    user = query.from_user
    results = []

    query_text = query.query.lower()

    if query_text in ["", "بازی", "دوستانه", "بازی با دوستان", "minesweeper", "مین", "کاشف مین", "مین‌یاب"]:
        game_id_cf = f"inline_{user.id}_{query.id}_cf"
        games[game_id_cf] = {
            'board': create_board(),
            'player1': user.id,
            'player2': None,
            'current_turn': 1,
            'status': 'waiting',
            'chat_id': user.id,
            'timer_task': None,
            'time_left': 60,
            'message_id': None
        }

        p1_name = user.first_name
        text_cf = f"{p1_name} شما رو به بازی دوز دعوت کرد!\n\nبرای پیوستن کلیک کن."

        keyboard_cf = InlineKeyboardMarkup([[
            InlineKeyboardButton("بازی با دوستان", callback_data=f"join:{game_id_cf}")
        ]])

        results.append(
            InlineQueryResultArticle(
                id=game_id_cf,
                title="دوز",
                description="با دوستت دوز بازی کن!",
                input_message_content=InputTextMessageContent(
                    message_text=text_cf,
                    parse_mode='Markdown'
                ),
                reply_markup=keyboard_cf
            )
        )

        game_id_ms = f"ms_inline_{user.id}_{query.id}"
        minesweeper_games[game_id_ms] = {
            'board': ms_create_board(),
            'mines': set(),
            'player1': user.id,
            'player2': None,
            'current_turn': 1,
            'status': 'waiting',
            'chat_id': user.id,
            'timer_task': None,
            'time_left': 60,
            'message_id': None,
            'first_click': None,
            'scores': {1: 0, 2: 0},
        }

        text_ms = f"{p1_name} شما رو به مین‌یاب دعوت کرد!\n\nبرای پیوستن کلیک کن."

        keyboard_ms = InlineKeyboardMarkup([[
            InlineKeyboardButton("مین‌یاب", callback_data=f"ms_join:{game_id_ms}")
        ]])
        results.append(
            InlineQueryResultArticle(
                id=game_id_ms,
                title="مین‌یاب",
                description="با دوستت مین‌یاب بازی کن!",
                input_message_content=InputTextMessageContent(
                    message_text=text_ms,
                    parse_mode='Markdown'
                ),
                reply_markup=keyboard_ms
            )
        )

        results.append(
            InlineQueryResultArticle(
                id="help_minesweeper",
                title="راهنمای مین‌یاب",
                description="یاد بگیر چطور مین‌یاب بازی کنی",
                input_message_content=InputTextMessageContent(
                    message_text="""راهنمای مین‌یاب:

هدف: بیشتر از حریف مین پیدا کن!

نحوه بازی:
1. هر بازیکن به نوبت یه خونه رو کلیک می‌کنه
2. اگر مین باشه: +1 امتیاز و یه نوبت دیگه
3. اگر مین نباشه: عدد مین‌های اطراف رو نشون میده
4. بازی تا پیدا شدن همه مین‌ها ادامه داره
5. هرکس مین بیشتری پیدا کنه برنده است

نکته: از اعداد برای حدس زدن مین‌ها استفاده کن!""",
                    parse_mode='Markdown'
                )
            )
        )

        results.append(
            InlineQueryResultArticle(
                id="help_connect_four",
                title="راهنمای دوز",
                description="یاد بگیر چطور دوز بازی کنی",
                input_message_content=InputTextMessageContent(
                    message_text=""" 🎮** نحوه بازی Connect Four :**
1️⃣ با انتخاب هر دکمه ( 1 تا 7 ) یک مهره داخل ستون مربوطه می افتد و در پایین ترین محل خالی قرار میگیرد.

2️⃣ دو نفر به نوبت بازی میکنند و به یک بازیکن رنگ 🔵 و بازیکن دیگر رنگ 🔴 اختصاص داده میشود.

3️⃣ بازیکنان باید تلاش کنند تا 4 مهره از رنگ خود را به صورت عمودی، افقی یا مایل مانند شکل زیر ردیف کنند.

به 3 مثال زیر توجه کنید :

1- برنده : آبی    روش: افقی
⚪️⚪️⚪️⚪️⚪️⚪️⚪️
⚪️⚪️⚪️⚪️⚪️⚪️⚪️
⚪️⚪️⚪️⚪️⚪️⚪️⚪️
⚪️⚪️⚪️🔴⚪️⚪️⚪️
⚪️🔵🔵🔵🔵⚪️⚪️
⚪️🔴🔴🔴🔵⚪️⚪️
1️⃣2️⃣3️⃣4️⃣5️⃣6️⃣7️⃣

2- برنده : قرمز     روش: مایل
⚪️⚪️⚪️⚪️⚪️⚪️⚪️
⚪️⚪️⚪️⚪️⚪️⚪️⚪️
⚪️⚪️⚪️⚪️⚪️⚪️🔴
⚪️⚪️⚪️⚪️⚪️🔴🔵
⚪️⚪️⚪️⚪️🔴🔵🔴
🔴⚪️🔵🔴🔵🔵🔵
1️⃣2️⃣3️⃣4️⃣5️⃣6️⃣7️⃣

3- برنده : آبی      روش: عمودی
⚪️⚪️⚪️⚪️⚪️⚪️⚪️
⚪️⚪️⚪️⚪️⚪️⚪️⚪️
⚪️⚪️⚪️🔵⚪️⚪️⚪️
⚪️⚪️⚪️🔵🔴⚪️⚪️
⚪️⚪️⚪️🔵🔴⚪️⚪️
⚪️⚪️⚪️🔵🔴⚪️⚪️
1️⃣2️⃣3️⃣4️⃣5️⃣6️⃣7️⃣

دو سه بار بازی کنی قلق کار دستت میاد ❤️‍🔥
بازی خوبی داشته باشی 🫂""",
                    parse_mode='Markdown'
                )
            )
        )

        await query.answer(results, cache_time=0)

async def handle_move(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data.split(":")
    if data[0] != "move": return
    game_id, col = data[1], int(data[2])

    if game_id not in games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = games[game_id]
    if game['status'] != 'playing':
        await query.answer("بازی تمام شد.", show_alert=True)
        return

    user_id = query.from_user.id
    if user_id not in [game['player1'], game['player2']]:
        await query.answer("شما بازیکن نیستید!", show_alert=True)
        return

    player_num = 1 if user_id == game['player1'] else 2
    if player_num != game['current_turn']:
        await query.answer("نوبت شما نیست!", show_alert=True)
        return

    if not await check_subscription(user_id, context.bot):
        channel_list = "\n".join([f"- {ch['name']} (@{ch['username'][1:]})" for ch in load_channels()])
        await query.answer(
            f"عضو کانال‌ها شو:\n{channel_list}",
            show_alert=True
        )
        return

    board = game['board']
    if not is_valid_move(board, col):
        await query.answer("این ستون پر است!", show_alert=True)
        return

    drop_piece(board, col, player_num)
    next_turn = 2 if player_num == 1 else 1
    game['current_turn'] = next_turn

    if game.get('timer_task'):
        game['timer_task'].cancel()

    p1 = await context.bot.get_chat(game['player1'])
    p2 = await context.bot.get_chat(game['player2'])

    def format_name(name, user_id, chat_id):
        medal = get_user_medal(user_id)
        crowns_global = get_user_crowns(user_id)
        crowns_group = get_user_crowns_group(user_id, chat_id)
        parts = [name]
        if medal:
            parts.append(medal)
        parts.append("|")
        if crowns_global > 0:
            parts.append(f"👑{crowns_global}")
        if crowns_group > 0:
            parts.append(f" medals{crowns_group}")
        return " ".join(parts).strip()

    p1_display = format_name(p1.first_name, p1.id, game['chat_id'])
    p2_display = format_name(p2.first_name, p2.id, game['chat_id'])

    winner = check_winner(board)
    if winner:
        if game.get('timer_task'):
            game['timer_task'].cancel()
        game['timer_task'] = None
        game['status'] = 'finished'

        winner_id = game['player1'] if player_num == 1 else game['player2']
        loser_id = game['player2'] if winner_id == game['player1'] else game['player1']
        update_scores(winner_id, game['chat_id'], win=True)
        update_scores(loser_id, game['chat_id'], win=False)

        winner_user = await context.bot.get_chat(winner_id)
        text = "بازی تمام شد!\n"
        text += f"برنده: {winner_user.first_name}\n"
        text += "۴ تا پشت سر هم!\n\n"
        text += format_board(board)

        await query.edit_message_text(text=text, parse_mode='HTML', reply_markup=None)
        await query.answer()
        return

    if is_full(board):
        if game.get('timer_task'):
            game['timer_task'].cancel()
        game['timer_task'] = None
        game['status'] = 'finished'

        update_scores(game['player1'], game['chat_id'], draw=True)
        update_scores(game['player2'], game['chat_id'], draw=True)

        text = "بازی تمام شد!\n"
        text += "مساوی!\n\n"
        text += format_board(board)

        await query.edit_message_text(text=text, parse_mode='HTML', reply_markup=None)
        await query.answer()
        return

    next_player_name = p1.first_name if next_turn == 1 else p2.first_name
    text = f"{p1_display} ({PLAYER1_SYMBOL})\nدر برابر\n{p2_display} ({PLAYER2_SYMBOL})\n\n"
    text += format_board(board)
    text += f"\n\nنوبت: {next_player_name}"

    await query.edit_message_text(text=text, reply_markup=build_keyboard(game_id, user_id))
    start_advanced_timer(game_id, context)

async def resign(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    game_id = query.data.split(":")[1]
    user_id = query.from_user.id

    if game_id not in games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = games[game_id]
    if user_id not in [game['player1'], game['player2']]:
        await query.answer("فقط بازیکنان می‌تونن انصراف بدن.", show_alert=True)
        return

    opponent_id = game['player2'] if user_id == game['player1'] else game['player2']
    update_scores(opponent_id, game['chat_id'], win=True)
    update_scores(user_id, game['chat_id'], win=False)

    if game.get('timer_task'):
        game['timer_task'].cancel()
        game['timer_task'] = None

    opponent = await context.bot.get_chat(opponent_id)
    text = "بازی تمام شد!\n"
    text += f"برنده: {opponent.first_name}\n"
    text += f"انصراف: {query.from_user.first_name}\n\n"
    text += format_board(game['board'])

    game['status'] = 'finished'
    await query.edit_message_text(text=text, parse_mode='HTML', reply_markup=None)
    await query.answer()

async def resend_game(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    game_id = query.data.split(":")[1]
    user_id = query.from_user.id

    if game_id not in games:
        await query.answer("بازی وجود ندارد.", show_alert=True)
        return

    game = games[game_id]
    chat_id = game['chat_id']

    if user_id not in [game['player1'], game['player2']]:
        await query.answer("فقط بازیکنان می‌تونن ارسال کنن.", show_alert=True)
        return

    if not await is_bot_admin(context.bot, chat_id):
        await query.answer("ربات ادمین نیست.", show_alert=True)
        return

    if game['status'] != 'playing':
        await query.answer("بازی در جریان نیست.", show_alert=True)
        return

    try:
        await context.bot.unpin_chat_message(chat_id)
    except:
        pass

    p1 = await context.bot.get_chat(game['player1'])
    p2 = await context.bot.get_chat(game['player2'])
    p1_name = p1.first_name
    p2_name = p2.first_name
    current_turn_name = p1_name if game['current_turn'] == 1 else p2_name

    def format_name(name, user_id, chat_id):
        medal = get_user_medal(user_id)
        crowns_global = get_user_crowns(user_id)
        crowns_group = get_user_crowns_group(user_id, chat_id)
        parts = [name]
        if medal:
            parts.append(medal)
        parts.append("|")
        if crowns_global > 0:
            parts.append(f"👑{crowns_global}")
        if crowns_group > 0:
            parts.append(f" medals{crowns_group}")
        return " ".join(parts).strip()

    p1_display = format_name(p1_name, p1.id, chat_id)
    p2_display = format_name(p2_name, p2.id, chat_id)

    board = game['board']
    text = f"{p1_display} ({PLAYER1_SYMBOL})\nدر برابر\n{p2_display} ({PLAYER2_SYMBOL})\n\n"
    text += format_board(board)
    text += f"\n\nنوبت: {current_turn_name}"

    try:
        msg = await context.bot.send_message(
            chat_id=chat_id,
            text=text,
            reply_markup=build_keyboard(game_id, user_id)
        )
        await context.bot.pin_chat_message(chat_id=chat_id, message_id=msg.message_id)
        await context.bot.send_message(
            chat_id=chat.id,
            text="بازی مجدداً ارسال شد.",
            disable_notification=True
        )
        await query.answer("بازی ارسال شد.")
        start_advanced_timer(game_id, context)
    except Exception as e:
        await query.answer(f"خطا: {str(e)}", show_alert=True)

async def show_global_leaderboard(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user = query.from_user
    leaderboard = get_global_leaderboard()

    if not leaderboard:
        await query.answer()
        await query.edit_message_text("هنوز بازی انجام نشده!")
        return
    user_rank = get_user_global_rank(user.id)
    user_score = next((score for name, score in leaderboard if name == user.first_name), 0)

    text = f"رتبه شما: #{user_rank}\n"
    text += f"امتیاز شما: {user_score}\n\n"
    text += "۵۰ برتر:\n\n"

    for i, (name, score) in enumerate(leaderboard, 1):
        medal = get_user_medal_by_rank(i)
        medal_text = f"{medal} " if medal else ""
        text += f"{medal_text}{i}. {name} - {score}\n"
        if i >= 20:
            break

    keyboard = [[InlineKeyboardButton("بازگشت", callback_data="back_to_start")]]
    await query.answer()
    await query.edit_message_text(text=text, parse_mode='HTML', reply_markup=InlineKeyboardMarkup(keyboard))

def get_user_medal_by_rank(rank):
    if rank == 1:
        return "💎"
    elif rank == 2:
        return "🥇"
    elif rank == 3:
        return "🥈"
    else:
        return ""

async def global_leaderboard_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat

    if chat.type == "private":
        await update.message.reply_text("این دستور فقط در گروه است.")
        return

    leaderboard = get_global_leaderboard()
    if not leaderboard:
        await update.message.reply_text("هنوز بازی انجام نشده!")
        return

    user_rank = get_user_global_rank(user.id)
    user_score = next((score for name, score in leaderboard if name == user.first_name), 0)

    text = f"رتبه شما: #{user_rank}\n"
    text += f"امتیاز شما: {user_score}\n\n"
    text += "۵۰ برتر:\n\n"

    for i, (name, score) in enumerate(leaderboard, 1):
        medal = get_user_medal_by_rank(i)
        medal_text = f"{medal} " if medal else ""
        text += f"{medal_text}{i}. {name} - {score}\n"
        if i >= 20:
            break

    await update.message.reply_text(text)

async def show_group_leaderboard(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    chat_id = query.message.chat_id
    user = query.from_user
    leaderboard = get_group_leaderboard(chat_id)
    title = (await context.bot.get_chat(chat_id)).title or "این گروه"

    if not leaderboard:
        await query.answer()
        await query.edit_message_text(f"در {title} هنوز بازی نشده!")
        return

    user_rank = get_user_global_rank(user.id)
    user_score = next((score for name, score in leaderboard if name == user.first_name), 0)

    text = f"رتبه شما در {title}: #{user_rank}\n"
    text += f"امتیاز شما: {user_score}\n\n"
    text += f"برترین‌های {title}:\n\n"

    for i, (name, score) in enumerate(leaderboard, 1):
        if i == 1:
            text += f"🥇 1. {name} - {score}\n"
        elif i == 2:
            text += f"🥈 2. {name} - {score}\n"
        elif i == 3:
            text += f"🥉 3. {name} - {score}\n"
        else:
            text += f"{i}. {name} - {score}\n"
        if i >= 20:
            break

    keyboard = [[InlineKeyboardButton("بازگشت", callback_data="back_to_start")]]
    await query.answer()
    await query.edit_message_text(text=text, parse_mode='HTML', reply_markup=InlineKeyboardMarkup(keyboard))

async def group_leaderboard_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat = update.effective_chat
    user = update.effective_user

    if chat.type == "private":
        await update.message.reply_text("این دستور فقط در گروه است.")
        return

    leaderboard = get_group_leaderboard(chat.id)
    title = chat.title or "این گروه"

    if not leaderboard:
        await update.message.reply_text(f"در {title} هنوز بازی نشده!")
        return

    user_rank = get_user_global_rank(user.id)
    user_score = next((score for name, score in leaderboard if name == user.first_name), 0)

    text = f"رتبه شما در {title}: #{user_rank}\n"
    text += f"امتیاز شما: {user_score}\n\n"
    text += f"برترین‌های {title}:\n\n"

    for i, (name, score) in enumerate(leaderboard, 1):
        if i == 1:
            text += f"🥇 1. {name} - {score}\n"
        elif i == 2:
            text += f"🥈 2. {name} - {score}\n"
        elif i == 3:
            text += f"🥉 3. {name} - {score}\n"
        else:
            text += f"{i}. {name} - {score}\n"
        if i >= 20:
            break

    await update.message.reply_text(text)

async def my_stats(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_permission(update, context):
        return

    user = update.effective_user
    chat = update.effective_chat

    register_user(user.id, user.first_name, user.username)
    if chat.type != "private":
        register_group(chat.id, chat.title)

    conn = sqlite3.connect('game_bot.db')
    c = conn.cursor()
    c.execute('''
        SELECT total_wins, total_losses, total_draws, total_score
        FROM users WHERE user_id = ?
    ''', (user.id,))
    row = c.fetchone()
    if row:
        total_wins, total_losses, total_draws, total_score = row
    else:
        total_wins = total_losses = total_draws = total_score = 0

    global_rank = get_user_global_rank(user.id)
    medal = get_user_medal(user.id)
    crowns_global = get_user_crowns(user.id)
    crowns_group = 0
    if chat.type != "private":
        crowns_group = get_user_crowns_group(user.id, chat.id)
    conn.close()

    text = f"آمار شما:\n\n"
    text += f"رتبه: #{global_rank}\n"
    text += f"امتیاز: {total_score}\n"
    if medal:
        text += f"مدال: {medal}\n"
    if crowns_global > 0 or crowns_group > 0:
        text += f"تاج هفتگی: {crowns_global + crowns_group}\n\n"
    else:
        text += "\n"

    text += f"برد: {total_wins}\n"
    text += f"باخت: {total_losses}\n"
    text += f"مساوی: {total_draws}\n"

    keyboard = [[InlineKeyboardButton("بازگشت", callback_data="back_to_start")]]

    if update.callback_query:
        try:
            await update.callback_query.edit_message_text(text=text, reply_markup=InlineKeyboardMarkup(keyboard))
            await update.callback_query.answer()
        except:
            await update.callback_query.message.reply_text(text, reply_markup=InlineKeyboardMarkup(keyboard))
    else:
        await update.message.reply_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

async def help_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_permission(update, context):
        return

    text = """
راهنمای دوز:

۱. یه ستون (۱-۷) رو انتخاب کن
۲. بازیکنان به نوبت بازی می‌کنن
۳. ۴ مهره پشت سر هم بچین (افقی، عمودی یا مورب)
۴. هر کس زودتر ۴ تا بچینه برنده است
۵. اگر صفحه پر شد مساوی

موفق باشی!
"""
    keyboard = InlineKeyboardMarkup([[
        InlineKeyboardButton("افزودن به گروه", url="https://t.me/YourBotUsername?startgroup=start")
    ]])
    await update.message.reply_text(text, parse_mode='Markdown', reply_markup=keyboard)

async def promote_user(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    message = update.message

    if chat.type == "private":
        await update.message.reply_text("این دستور فقط در گروه است.")
        return

    if not message.reply_to_message:
        await update.message.reply_text("روی پیام کاربر ریپلای کن.")
        return

    target_user = message.reply_to_message.from_user

    if not await is_admin(context.bot, chat.id, user.id):
        await update.message.reply_text("فقط ادمین‌ها می‌تونن ترفیع بدن.")
        return

    grant_access(target_user.id, chat.id, user.id)
    await update.message.reply_text(
        f"{target_user.first_name} ترفیع شد.",
        parse_mode='HTML'
    )

async def demote_user(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    message = update.message

    if chat.type == "private":
        await update.message.reply_text("این دستور فقط در گروه است.")
        return

    if not message.reply_to_message:
        await update.message.reply_text("روی پیام کاربر ریپلای کن.")
        return

    target_user = message.reply_to_message.from_user

    if not await is_admin(context.bot, chat.id, user.id):
        await update.message.reply_text("فقط ادمین‌ها می‌تونن عزل کنن.")
        return

    revoke_access(target_user.id, chat.id)
    await update.message.reply_text(
        f"{target_user.first_name} عزل شد.",
        parse_mode='HTML'
    )

async def back_to_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await start(update, context)

async def welcome_new_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat_member = update.chat_member
    if chat_member.new_chat_member.status in ["administrator", "creator"] and chat_member.new_chat_member.user.id == context.bot.id:
        try:
            owner = await context.bot.get_chat(chat_member.chat.id)
            await context.bot.send_message(
                chat_id=owner.id,
                text=f"ربات در {chat_member.chat.title} نصب شد!\n\n"
                     "دستورات:\n"
                     "دوز - شروع بازی دوز\n"
                     "مین‌یاب - شروع بازی مین‌یاب\n"
                     "آمار من - آمار شما\n"
                     "لیدربورد گروه - برترین‌های گروه\n"
                     "لیدربورد کلی - برترین‌های کل\n\n"
                     "دستورات ادمین:\n"
                     "ترفیع - دسترسی دادن به کاربر\n"
                     "عزل - گرفتن دسترسی از کاربر"
            )
        except:
            pass

# --- اصلی ---
def main():
    init_db()
    app = Application.builder().token(TOKEN).build()

    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("admin", admin_panel))
    app.add_handler(CommandHandler("end_week", end_week_command))

    app.add_handler(MessageHandler(filters.Regex(r"^دوز$"), start_game))
    app.add_handler(MessageHandler(filters.Regex(r"^مین‌یاب$"), start_minesweeper_game))
    app.add_handler(MessageHandler(filters.Regex(r"^آمار من$"), my_stats))
    app.add_handler(MessageHandler(filters.Regex(r"^لیدربورد گروه$"), group_leaderboard_command))
    app.add_handler(MessageHandler(filters.Regex(r"^لیدربورد کلی$"), global_leaderboard_command))
    app.add_handler(MessageHandler(filters.Regex(r"^راهنما$"), help_command))
    app.add_handler(MessageHandler(filters.Regex(r"^راهنمای مین‌یاب$"), ms_help_command))
    app.add_handler(MessageHandler(filters.Regex(r"^ترفیع$") & filters.REPLY, promote_user))
    app.add_handler(MessageHandler(filters.Regex(r"^عزل$") & filters.REPLY, demote_user))

    app.add_handler(CallbackQueryHandler(join_game, pattern=r"^join:"))
    app.add_handler(CallbackQueryHandler(handle_move, pattern=r"^move:"))
    app.add_handler(CallbackQueryHandler(resign, pattern=r"^resign:"))
    app.add_handler(CallbackQueryHandler(show_group_leaderboard, pattern=r"^group_lb"))
    app.add_handler(CallbackQueryHandler(show_global_leaderboard, pattern=r"^global_lb"))
    app.add_handler(CallbackQueryHandler(my_stats, pattern=r"^my_stats"))
    app.add_handler(CallbackQueryHandler(back_to_start, pattern=r"^back_to_start"))
    app.add_handler(CallbackQueryHandler(resend_game, pattern=r"^resend:"))

    app.add_handler(CallbackQueryHandler(ms_join_game, pattern=r"^ms_join:"))
    app.add_handler(CallbackQueryHandler(ms_handle_move, pattern=r"^ms_move:"))
    app.add_handler(CallbackQueryHandler(ms_resign, pattern=r"^ms_resign:"))
    app.add_handler(CallbackQueryHandler(ms_resend_game, pattern=r"^ms_resend:"))

    app.add_handler(CallbackQueryHandler(add_channel_step1, pattern=r"^add_channel_step1$"))
    app.add_handler(CallbackQueryHandler(remove_channel_step1, pattern=r"^remove_channel_step1$"))
    app.add_handler(CallbackQueryHandler(confirm_remove_channel, pattern=r"^confirm_remove_channel:"))

    app.add_handler(CallbackQueryHandler(show_broadcast_menu, pattern=r"^broadcast_menu$"))
    app.add_handler(CallbackQueryHandler(start_broadcast_private, pattern=r"^broadcast_private_start$"))
    app.add_handler(CallbackQueryHandler(start_broadcast_group, pattern=r"^broadcast_group_start$"))
    app.add_handler(CallbackQueryHandler(start_forward_private, pattern=r"^forward_private_start$"))
    app.add_handler(CallbackQueryHandler(start_forward_group, pattern=r"^forward_group_start$"))

    app.add_handler(CallbackQueryHandler(show_admin_stats, pattern="^admin_stats$"))
    app.add_handler(CallbackQueryHandler(back_to_admin, pattern="^back_to_admin$"))

    app.add_handler(InlineQueryHandler(handle_inline))
    app.add_handler(ChatMemberHandler(welcome_new_admin, ChatMemberHandler.CHAT_MEMBER))

    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, receive_add_channel_data))
    app.add_handler(MessageHandler(filters.TEXT | filters.PHOTO | filters.VIDEO | filters.Document.ALL | filters.AUDIO | filters.VOICE, receive_broadcast_message))
    print("ربات در حال اجراست...")
    app.run_polling()

if __name__ == '__main__':
    main()