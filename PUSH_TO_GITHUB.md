# Pushing to GitHub

The project is configured with the remote: `https://github.com/vibeai-casv/playqzv4.git`

## 1. Push Current Changes
I have already committed the latest changes (AI generation, Super Admin, Activity Logs). To push them to the `Question-Management` branch:

```bash
git push origin Question-Management
```

## 2. Merge to Main (Optional)
If you want to update the main branch:

```bash
git checkout main
git merge Question-Management
git push origin main
git checkout Question-Management
```

## 3. Troubleshooting
If you are asked for a password, use a **Personal Access Token** (PAT) instead of your account password.
1.  Go to GitHub Settings > Developer settings > Personal access tokens.
2.  Generate a new token with `repo` permissions.
3.  Use this token as your password when prompted.
