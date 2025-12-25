# Classconnect

ClassConnect is a comprehensive **student-teacher portal** designed for seamless communication, collaboration, and academic management within a class. It provides a centralized platform for students to access class feeds, submit feedback, view notifications, participate in polls, and download resources such as question papers and timetables.

---

## 🚀 Features

- **Student Dashboard:** All-in-one access to class updates, files, polls, and academic resources.
- **Class Feed:** Students and teachers can post announcements, updates, and share files.
- **File Management:** Upload, download, and manage important class documents.
- **Feedback System:** Students can provide feedback to teachers with optional anonymity.
- **Polls:** Participate in class polls and view results in real-time.
- **Notifications:** Stay updated with the latest announcements and alerts.
- **Teacher’s Corner:** Dedicated section for teacher-specific interactions.
- **Question Papers & Timetable:** Easy access to academic resources.
- **Role-Based Access:** Only students can access student-specific features; teachers/admins have elevated permissions.

---

## 🛠️ Tech Stack

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript
- **Database:** MySQL
- **Version Control:** Git / GitHub

---

👨‍💻 Usage

Students log in to access class feeds, upload files, submit feedback, participate in polls, and view question papers or timetable.

Teachers/Admins can post announcements, manage polls, and monitor feedback.

## 🚀 Deployment to InfinityFree Hosting

1. **Prepare the Database:**

   - Log in to your InfinityFree account and create a MySQL database.
   - Import the `classconnectmain.sql` file into your database using phpMyAdmin or the database manager provided by InfinityFree.

2. **Configure Database Settings:**

   - Edit `config.php` and update the database variables with your InfinityFree MySQL details:
     - `$servername`: Your MySQL host (e.g., sqlXXX.epizy.com)
     - `$username`: Your MySQL username
     - `$password`: Your MySQL password
     - `$dbname`: Your database name

3. **Upload Files:**

   - Zip all project files (excluding `.git` folder and unnecessary files like SQL dumps if already imported).
   - Upload the zip to your InfinityFree hosting via File Manager or FTP.
   - Extract the files in the public_html directory (or subdirectory if needed).

4. **Set Permissions:**

   - Ensure the `Uploads/` folder and subfolders have write permissions (755 or 777) for file uploads.

5. **Test the Application:**
   - Access your site URL and test key features: login, signup, dashboards, file uploads, etc.
   - If issues arise, check error logs in InfinityFree's control panel.

---

contact

ajilaries20@gmail.com
