# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os

def count_lines_in_file(filepath):
    """Count the number of lines in a single file."""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            return len(f.readlines())
    except (UnicodeDecodeError, PermissionError, IOError) as e:
        print(f"Could not read {filepath}: {str(e)}")
        return 0

def count_python_lines(directory):
    """Count lines in all Python files in directory and subdirectories."""
    total_lines = 0
    file_count = 0
    results = []
    
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.py'):
                filepath = os.path.join(root, file)
                lines = count_lines_in_file(filepath)
                results.append((filepath, lines))
                total_lines += lines
                file_count += 1
    
    # Print detailed results
    print("\nDetailed File Results:")
    print("-" * 60)
    for filepath, lines in results:
        print(f"{lines:6} | {filepath}")
    
    # Print summary
    print("\n" + "=" * 60)
    print("SUMMARY:")
    print(f"Total Python files: {file_count}")
    print(f"Total lines of code: {total_lines}")
    print("=" * 60)
    
    return total_lines, file_count

if __name__ == "__main__":
    project_dir = input("Enter the path to your project directory (or press Enter for current directory): ").strip()
    
    if not project_dir:
        project_dir = os.getcwd()
    
    print(f"\nCounting lines of Python code in: {project_dir}")
    total_lines, file_count = count_python_lines(project_dir)