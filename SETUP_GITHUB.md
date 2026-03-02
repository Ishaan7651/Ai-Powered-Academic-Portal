# GitHub Setup Instructions

Follow these steps to push your project to GitHub.

## Step 1: Create Repository on GitHub

1. Go to https://github.com/Ishaan7651
2. Click the "+" icon in the top right corner
3. Select "New repository"
4. Repository name: `Ai-Powered-Academic-Portal`
5. Description: `AI-powered academic management system with intelligent content generation`
6. Choose "Public" (or Private if you prefer)
7. **DO NOT** initialize with README, .gitignore, or license (we already have these)
8. Click "Create repository"

## Step 2: Run These Commands

Open your terminal in the project directory and run:

```bash
# Initialize git repository
git init

# Add all files
git add .

# Create initial commit
git commit -m "Initial commit: AI-Powered Academic Portal with comprehensive features"

# Add remote repository
git remote add origin https://github.com/Ishaan7651/Ai-Powered-Academic-Portal.git

# Rename branch to main
git branch -M main

# Push to GitHub
git push -u origin main
```

## Step 3: Verify Upload

1. Go to https://github.com/Ishaan7651/Ai-Powered-Academic-Portal
2. Verify all files are uploaded
3. Check that README.md displays correctly

## Step 4: Add Repository Topics (Optional)

On your GitHub repository page:
1. Click "Add topics"
2. Add relevant tags: `php`, `codeigniter`, `ai`, `education`, `academic-portal`, `openai`, `mysql`, `learning-management-system`

## Step 5: Enable GitHub Pages (Optional)

If you want to host documentation:
1. Go to Settings → Pages
2. Select source branch
3. Save

## Troubleshooting

### If you get authentication errors:
- Use GitHub CLI: `gh auth login`
- Or use Personal Access Token instead of password
- Or use SSH: `git remote set-url origin git@github.com:Ishaan7651/Ai-Powered-Academic-Portal.git`

### If repository already exists:
```bash
git remote remove origin
git remote add origin https://github.com/Ishaan7651/Ai-Powered-Academic-Portal.git
git push -u origin main --force
```

### If you need to update after initial push:
```bash
git add .
git commit -m "Your commit message"
git push
```

## Next Steps After Upload

1. Add a repository description on GitHub
2. Add topics/tags for discoverability
3. Consider adding screenshots to README
4. Set up GitHub Actions for CI/CD (optional)
5. Enable issue templates
6. Add a CHANGELOG.md file for version tracking
