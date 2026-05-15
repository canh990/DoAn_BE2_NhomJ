@echo off
cd /d "d:\DoAn_BE2_NhomJ"

echo ========================================
echo 1. Checking git status...
echo ========================================
git status
echo.

echo ========================================
echo 2. Staging all changes...
echo ========================================
git add -A
echo.

echo ========================================
echo 3. Current status after staging...
echo ========================================
git status
echo.

echo ========================================
echo 4. Committing changes...
echo ========================================
git commit -m "Search" -m "Add message search functionality for 1-1 and group chats

- Implement real-time search in 1-1 conversations
- Implement real-time search in group chats
- Add search UI with dropdown results
- Support keyword matching with LIKE queries
- Highlight messages when found (pulse animation)
- Pagination: 20 results per page
- Debounce: 300ms for performance
- Security: SQL injection and XSS prevention
- Authorization: User membership verification

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"
echo.

echo ========================================
echo 5. Switching to master branch...
echo ========================================
git checkout master
echo.

echo ========================================
echo 6. Pulling latest master...
echo ========================================
git pull origin master
echo.

echo ========================================
echo 7. Merging search branch into master...
echo ========================================
git merge laravel13/Anh/8-Search
echo.

echo ========================================
echo 8. Pushing to origin master...
echo ========================================
git push origin master
echo.

echo ========================================
echo 9. Final status...
echo ========================================
git log --oneline -5
echo.

echo ========================================
echo ALL DONE! Merge and push completed!
echo ========================================
pause
