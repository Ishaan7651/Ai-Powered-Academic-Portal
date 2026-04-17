# Testing Publish Buttons

All three AI generation features (Quiz, Assignment, Question Paper) already have publish buttons implemented!

## What's Already There:

### 1. Quiz Generation (`generate_quiz.php`)
✅ Publish button exists
✅ Publish modal exists
✅ Backend endpoint: `simple_portal/publish_quiz`
✅ CSS styling: `.btn-publish`

### 2. Assignment Generation (`generate_assignment.php`)
✅ Publish button exists (line 1098)
✅ Publish modal exists (line 897)
✅ Backend endpoint: `simple_portal/publish_assignment`
✅ CSS styling: `.btn-publish` (line 540)

### 3. Question Paper Generation (`generate_question_paper.php`)
✅ Publish button exists (line 1165)
✅ Publish modal exists (line 983)
✅ Backend endpoint: `simple_portal/publish_question_paper`
✅ CSS styling: `.btn-publish` (line 535)

## How to Test:

1. **Fix API Keys First** (from previous instructions)
   - Get new Gemini API keys
   - Update `application/libraries/AI_service.php`

2. **Test Each Feature:**
   
   ### For Assignments:
   ```
   1. Go to: http://localhost:8000/simple_portal/generate_assignment
   2. Select a subject
   3. Upload a PDF resource
   4. Fill in assignment details
   5. Click "Generate Assignment"
   6. After generation, you should see THREE buttons:
      - Print Assignment (blue)
      - Publish to Students (purple) ← THIS ONE
      - Done (green)
   ```

   ### For Question Papers:
   ```
   1. Go to: http://localhost:8000/simple_portal/generate_question_paper
   2. Select a subject
   3. Upload a PDF resource
   4. Configure paper settings
   5. Click "Generate Paper"
   6. After generation, you should see THREE buttons:
      - Print Paper (blue)
      - Publish to Students (purple) ← THIS ONE
      - Done (green)
   ```

   ### For Quizzes:
   ```
   1. Go to: http://localhost:8000/simple_portal/generate_quiz
   2. Select a subject
   3. Upload a PDF resource
   4. Configure quiz settings
   5. Click "Generate Quiz"
   6. After generation, you should see THREE buttons:
      - Print Quiz (blue)
      - Publish to Students (purple) ← THIS ONE
      - Done (green)
   ```

## If Buttons Don't Appear:

### Check 1: Browser Console
Open browser DevTools (F12) and check for JavaScript errors

### Check 2: Verify Generation Completed
The publish button only enables AFTER successful generation:
- `currentAssignmentId` must be set (for assignments)
- `currentPaperId` must be set (for question papers)
- `currentQuizId` must be set (for quizzes)

### Check 3: Subject Selection
Make sure you selected a subject before generating

### Check 4: Database
Verify the database fix was applied:
```sql
SHOW COLUMNS FROM ai_assignments LIKE 'type';
SHOW COLUMNS FROM ai_question_papers LIKE 'is_published';
SHOW COLUMNS FROM ai_quizzes LIKE 'is_published';
```

## What Happens When You Click Publish:

1. Modal appears asking for confirmation
2. Click "Publish Now"
3. AJAX request sent to backend
4. Database updated with:
   - `is_published = 1`
   - `published_at = current timestamp`
   - `subject_id = selected subject`
5. Success message appears
6. Students can now see it in their dashboard

## Student View:

After publishing, students enrolled in that subject will see:
- Assignments in: `simple_portal/student_assignments`
- Question Papers in: `simple_portal/student_question_papers`
- Quizzes in: `simple_portal/student_quizzes`

## Troubleshooting:

If publish button is disabled (grayed out):
1. Make sure generation completed successfully
2. Check browser console for errors
3. Verify `currentAssignmentId/currentPaperId/currentQuizId` is set
4. Ensure subject was selected

If publish fails:
1. Check application logs: `application/logs/log-YYYY-MM-DD.php`
2. Verify database columns exist
3. Check API key is valid
4. Ensure user is logged in as faculty
