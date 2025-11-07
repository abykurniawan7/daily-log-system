\# PROJECT RESTRUCTURE PLAN

\*\*Date:\*\* 2024-11-08  

\*\*Project:\*\* Test Daily Log System  

\*\*Database:\*\* system\_dailylog  

\*\*Branch:\*\* feature/multi-pic-role-restructure



---



\## 🎯 OBJECTIVES



\### 1. Multi-PIC System

\*\*Before:\*\* 1 project = 1 PIC (column `pic\_proyek\_id`)  

\*\*After:\*\* 1 project = multiple PICs (pivot table `project\_user`)



\### 2. Role Restructure

\*\*Before:\*\*

\- supervisi (super admin + PGB leader jadi satu)

\- perizinan (PKJ)

\- karyawan (PGB staff)



\*\*After:\*\*

\- supervisi (NEW super admin - akses PGB + PKJ)

\- kabag\_pgb (dari supervisi lama - khusus PGB)

\- perizinan (PKJ - tetap)

\- karyawan (PGB staff - tetap)



---



\## ✅ FASE 0: BACKUP (COMPLETED - 2024-11-08)



\### Backup Locations:

\- \*\*Database:\*\* `backup\_before\_restructure\_20241108.sql`

\- \*\*.env:\*\* `.env.backup\_20241108`

\- \*\*Files:\*\* `backups/before\_restructure/`

\- \*\*Git Branch:\*\* `feature/multi-pic-role-restructure`



\### Backup Details:

```

Database: system\_dailylog

Username: root

Password: 1234

```



\### Checklist:

\- \[x] Database backup via mysqldump

\- \[x] .env backup

\- \[x] Critical files backup (Models, Controllers, Seeders, Views)

\- \[x] Git commit: "chore: backup before major restructure"

\- \[x] Feature branch created

\- \[x] Documentation created



---



\## 🔄 NEXT: FASE 1 - DATABASE RESTRUCTURE



\### Tasks:

\- \[ ] Create migration: `project\_user` pivot table

\- \[ ] Create migration: Migrate existing PIC data to pivot

\- \[ ] Create migration: Update users role enum (add `kabag\_pgb`)

\- \[ ] Update `UserSeeder.php` (add new users)

\- \[ ] Run migrations: `php artisan migrate`

\- \[ ] Test database integrity



---



\## 🔄 FASE 2-8: UPCOMING



\- \*\*FASE 2:\*\* Model Layer (User.php, Project.php)

\- \*\*FASE 3:\*\* Middleware (CheckKabagPGB.php)

\- \*\*FASE 4:\*\* Controller Logic (Project, Activity, Dashboard)

\- \*\*FASE 5:\*\* View Layer (Blade templates)

\- \*\*FASE 6:\*\* Routes (web.php)

\- \*\*FASE 7:\*\* Testing (Manual + Unit tests)

\- \*\*FASE 8:\*\* Deployment (Merge to main)



---



\## 🚨 ROLLBACK PLAN (EMERGENCY USE ONLY!)



\### If Something Goes Wrong:

```bash

\# 1. Stop immediately \& stash changes

git stash



\# 2. Switch back to main branch

git checkout main



\# 3. Restore database

C:\\laragon\\bin\\mysql\\mysql-8.0.30\\bin\\mysql.exe -u root -p1234 system\_dailylog < backup\_before\_restructure\_20241108.sql



\# 4. Restore .env

copy .env.backup\_20241108 .env



\# 5. Clear all cache

php artisan cache:clear

php artisan config:clear

php artisan route:clear

php artisan view:clear



\# 6. Restart Laragon services

```



\### Alternative Rollback (If Git Stash Fails):

```bash

\# Hard reset to last commit (DANGER! Lose all changes)

git reset --hard HEAD



\# Then follow steps 2-6 above

```



---



\## 📝 DATABASE STRUCTURE CHANGES



\### New Table: `project\_user` (Pivot)

```sql

id          BIGINT UNSIGNED AUTO\_INCREMENT PRIMARY KEY

project\_id  BIGINT UNSIGNED (FK to projects.id)

user\_id     BIGINT UNSIGNED (FK to users.id)

role\_type   ENUM('pic', 'contributor')

created\_at  TIMESTAMP

updated\_at  TIMESTAMP

UNIQUE KEY (project\_id, user\_id)

```



\### Modified Table: `users`

```sql

role ENUM(

&nbsp;   'supervisi',    -- NEW super admin

&nbsp;   'kabag\_pgb',    -- NEW from old supervisi

&nbsp;   'perizinan',    -- UNCHANGED

&nbsp;   'karyawan',     -- UNCHANGED

&nbsp;   'admin',        -- UNCHANGED (if exists)

&nbsp;   'guest'         -- UNCHANGED (if exists)

)

```



\### Deprecated Column: `projects.pic\_proyek\_id`

\*\*Status:\*\* Keep for backward compatibility (will be removed in future)



---



\## 👥 USER MAPPING PLAN



\### Existing Users (Need to Verify):

1\. \*\*Supervisor PGD\*\* → Will become `kabag\_pgb`

&nbsp;  - Current: role='supervisi', bagian='PGB'

&nbsp;  - After: role='kabag\_pgb', bagian='PGB'



2\. \*\*PGB Staff\*\* → Stay as `karyawan`

&nbsp;  - Budi, Ani, etc.

&nbsp;  - Need to add 2 more (Eko, Fitri)



3\. \*\*PKJ Staff\*\* → Stay as `perizinan`

&nbsp;  - Candra, Dewi

&nbsp;  - Both can edit PKJ projects



\### New User:

4\. \*\*Super Admin PGD\*\* → New `supervisi`

&nbsp;  - Email: superadmin@bpdbali.co.id

&nbsp;  - Role: 'supervisi'

&nbsp;  - Bagian: NULL (access all)



---



\## 📂 FILES TO BE MODIFIED



\### FASE 1 (Database):

\- \[ ] `database/migrations/xxxx\_create\_project\_user\_table.php` (NEW)

\- \[ ] `database/migrations/xxxx\_migrate\_existing\_pic\_to\_pivot.php` (NEW)

\- \[ ] `database/migrations/xxxx\_update\_users\_role\_enum.php` (NEW)

\- \[ ] `database/seeders/UserSeeder.php` (UPDATE)



\### FASE 2 (Models):

\- \[ ] `app/Models/User.php` (UPDATE)

\- \[ ] `app/Models/Project.php` (UPDATE)



\### FASE 3 (Middleware):

\- \[ ] `app/Http/Middleware/CheckKabagPGB.php` (NEW)

\- \[ ] `bootstrap/app.php` (UPDATE)



\### FASE 4 (Controllers):

\- \[ ] `app/Http/Controllers/ProjectController.php` (MAJOR UPDATE)

\- \[ ] `app/Http/Controllers/ActivityController.php` (UPDATE)

\- \[ ] `app/Http/Controllers/DashboardController.php` (UPDATE)

\- \[ ] `app/Http/Controllers/EmployeeController.php` (UPDATE)



\### FASE 5 (Views):

\- \[ ] `resources/views/projects/index.blade.php` (UPDATE)

\- \[ ] `resources/views/projects/create.blade.php` (UPDATE)

\- \[ ] `resources/views/projects/edit.blade.php` (UPDATE)

\- \[ ] `resources/views/projects/show.blade.php` (UPDATE)

\- \[ ] `resources/views/activities/create.blade.php` (UPDATE)

\- \[ ] `resources/views/activities/index.blade.php` (UPDATE)



\### FASE 6 (Routes):

\- \[ ] `routes/web.php` (UPDATE)



---



\## 📞 IMPORTANT NOTES



\### Database Connection:

```

Host: 127.0.0.1

Port: 3306

Database: system\_dailylog

Username: root

Password: 1234

```



\### Backup Locations (Verified):

```

✅ backup\_before\_restructure\_20241108.sql (root folder)

✅ .env.backup\_20241108 (root folder)

✅ backups/before\_restructure/\*.bak (6 files)

✅ backups/before\_restructure/views\_projects/ (folder)

✅ backups/before\_restructure/views\_activities/ (folder)

```



\### Git Info:

```

Current Branch: feature/multi-pic-role-restructure

Base Branch: main

Remote: origin/main

```



---



\## ⚠️ CRITICAL REMINDERS



1\. \*\*NEVER run migrations on production without testing first\*\*

2\. \*\*ALWAYS backup before major changes\*\* (✅ DONE)

3\. \*\*Test in development first\*\* (✅ CURRENT)

4\. \*\*Keep pic\_proyek\_id column\*\* until fully tested

5\. \*\*Communicate with team\*\* before deploying



---



\## 📅 IMPLEMENTATION TIMELINE



\- \*\*FASE 0:\*\* ✅ COMPLETED (2024-11-08)

\- \*\*FASE 1:\*\* 🔄 IN PROGRESS (Estimated: 1-2 hours)

\- \*\*FASE 2-3:\*\* ⏳ PENDING (Estimated: 1 hour)

\- \*\*FASE 4-5:\*\* ⏳ PENDING (Estimated: 3-4 hours)

\- \*\*FASE 6-7:\*\* ⏳ PENDING (Estimated: 2-3 hours)

\- \*\*FASE 8:\*\* ⏳ PENDING (Deployment)



\*\*Total Estimated Time:\*\* 7-10 hours of development work



---



\## 🆘 TROUBLESHOOTING CONTACTS



\*\*Developer:\*\* \[Your Name]  

\*\*Start Date:\*\* 2024-11-08  

\*\*Last Updated:\*\* 2024-11-08



\*\*For Questions:\*\*

\- Check this document first

\- Review commit history: `git log`

\- Check backup files before restoring

