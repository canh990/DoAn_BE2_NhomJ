#!/usr/bin/env python3
import subprocess
import sys
import os

os.chdir(r'd:\DoAn_BE2_NhomJ')

def run_cmd(cmd, description):
    print(f"\n{'='*50}")
    print(f"{description}")
    print(f"{'='*50}")
    try:
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        print(result.stdout)
        if result.stderr:
            print(f"STDERR: {result.stderr}")
        return result.returncode == 0
    except Exception as e:
        print(f"ERROR: {e}")
        return False

# Step 1: Check status
run_cmd("git status", "1. Checking git status...")

# Step 2: Stage all changes
run_cmd("git add -A", "2. Staging all changes...")

# Step 3: Check status after staging
run_cmd("git status", "3. Status after staging...")

# Step 4: Commit
commit_msg = 'Search\n\nAdd message search functionality for 1-1 and group chats\n\n- Implement real-time search in 1-1 conversations\n- Implement real-time search in group chats\n- Add search UI with dropdown results\n- Support keyword matching with LIKE queries\n- Highlight messages when found (pulse animation)\n- Pagination: 20 results per page\n- Debounce: 300ms for performance\n- Security: SQL injection and XSS prevention\n- Authorization: User membership verification\n\nCo-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>'

cmd = f'git commit -m "{commit_msg}"'
run_cmd(cmd, "4. Committing changes...")

# Step 5: Switch to master
run_cmd("git checkout master", "5. Switching to master branch...")

# Step 6: Pull latest master
run_cmd("git pull origin master", "6. Pulling latest master...")

# Step 7: Merge search branch
run_cmd("git merge laravel13/Anh/8-Search", "7. Merging search branch into master...")

# Step 8: Push to origin
run_cmd("git push origin master", "8. Pushing to origin master...")

# Step 9: Show latest commits
run_cmd("git log --oneline -5", "9. Latest commits...")

print("\n" + "="*50)
print("✅ ALL DONE! Merge and push completed!")
print("="*50)
