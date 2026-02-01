# Movie Database Web Application

A simple PHP-based movie management system with **admin-only CRUD operations** for movies, cast members and genres + public viewing access for all visitors.

Admin credentials are **hardcoded** (no registration/regular user system).

---

## Login Credentials

**Admin Account**  
Username: `admin`  
Password: `admin123`

(There is no separate "viewer" login — anyone can browse movies without logging in.)

---

## Features Implemented

### General / Public Features
- Browse all movies in a clean card/grid layout
- View detailed information about each movie (title, year, director, genre, rating, description, poster)
- See cast/actors list for each movie
- Search movies by:
  - Title (with AJAX live suggestions/autocomplete)
  - Genre (dropdown)
  - Year range (from–to)
  - Minimum rating
- Responsive design (basic mobile-friendly layout)

### Admin Features (after login)
- **Movies CRUD**
  - Add new movie (with poster image upload — jpg/jpeg/png, max 5MB)
  - Edit movie details
  - Delete movie (with confirmation)
  - Default "no-image.png" shown if no poster uploaded
- **Cast Management per movie**
  - Add cast member (actor name + optional character/role)
  - Delete cast member
  - View cast list for each movie
- **Genre Management** (assumed from your description — partial or planned)
  - View/add genres used in dropdowns
- Secure admin-only routes (`requireAdmin()`)

### Technical Features
- PDO with **prepared statements** (protection against SQL injection)
- **XSS protection** using `htmlspecialchars()`
- Session-based authentication + role check
- File upload validation for movie posters
- AJAX-powered title suggestions in search
- Confirmation dialogs before delete actions
- Clean separation of concerns (header, footer, init, session files)

---

## Project Structure (main files)
|── config/
│   └── db.php               ← Database connection
├── includes/
│   ├── session.php          ← Session & auth logic
│   ├── init.php             ← Admin auto-creation check
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/style.css
│   └── uploads/             ← Movie posters + no-image.png
├── ajax_search.php          ← Title autocomplete backend
├── index.php                ← Movie listing / home
├── search.php               ← Advanced search page
├── view.php                 ← Single movie detail
├── add.php                  ← Add new movie + image upload
├── edit.php                 ← (assumed) Edit movie
├── cast.php                 ← Manage cast for a movie
├── delete.php               ← (assumed) Delete handler
├── login.php                ← Admin login page
└── logout.php


---

## Security Measures

- PDO prepared statements everywhere
- All output escaped with `htmlspecialchars()`
- Admin routes protected with `requireAdmin()`
- Session regeneration after 30 minutes
- File upload validation (extension + size)
- Delete actions protected with JS `confirm()`

---

## Validation

**Server-side**
- Required fields check
- Valid year range
- Rating between 0–10
- Image type & size validation

**Client-side**
- HTML5 `required`, `min`, `max`, `step`, `accept` attributes
- Number/step validation on rating/year inputs

---

## User Interface Highlights

- Card-based movie grid
- Clean detail page layout
- Responsive search form
- Success/error alert messages
- AJAX title suggestions with keyboard navigation (↑↓ Enter Esc)
- Confirmation before destructive actions

---

## Limitations / Possible Improvements

- Only one hardcoded admin account (no user registration)
- No password change / forgot password
- No pagination on movie list
- Basic genre management (if not fully implemented yet)
- No advanced filtering/sorting on cast
- No movie reviews/ratings from users
- No search by actor/director

---

## Conclusion

This Movie Database application provides a solid foundation for managing movie information with secure admin CRUD operations and a pleasant public browsing experience. It demonstrates proper use of:

- PDO + prepared statements
- Session-based role management
- File uploads with validation
- AJAX for better UX
- Clean file organization and reusable includes

Perfect as a university/college web development project submission.

Good luck with your assignment! 🍿