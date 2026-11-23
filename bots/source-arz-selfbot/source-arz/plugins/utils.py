# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
def format_number(number):
    """Format a number with commas for thousands and proper decimal places."""
    if isinstance(number, float):
        num_str = f"{number:f}"
        if '.' in num_str:
            num_str = num_str.rstrip('0').rstrip('.') if '.' in num_str else num_str
    else:
        num_str = str(number)
    num_str = num_str.replace(',', '')
    if '.' in num_str:
        int_part, dec_part = num_str.split('.')
    else:
        int_part, dec_part = num_str, ''
    if len(int_part) > 3:
        formatted_int = ''
        while int_part:
            formatted_int = int_part[-3:] + (',' + formatted_int if formatted_int else '')
            int_part = int_part[:-3]
    else:
        formatted_int = int_part
    if dec_part:
        return f"{formatted_int}.{dec_part}"
    else:
        return formatted_int
def format_change(change):
    """Format the change value with appropriate indicators."""
    change = change.strip('()')
    if '(' in change:
        change = change.split('(')[1].split(')')[0]
    parts = change.split()
    if len(parts) > 1:
        change_value = parts[-1].replace(',', '')
    else:
        change_value = change.replace(',', '')
    try:
        formatted_change = format_number(change_value)
        if change.startswith('-'):
            return f"📉 {formatted_change}-"
        else:
            return f"📈 {formatted_change}+"
    except:
        return change
