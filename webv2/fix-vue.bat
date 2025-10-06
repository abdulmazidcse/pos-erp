@echo off
cd /d %~dp0
echo Cleaning project...
del package-lock.json
rd /s /q node_modules
npm cache clean --force

echo Installing dependencies...
npm install

echo Fixing audit issues...
npm audit fix --force

echo Running project...
npm run serve
pause
