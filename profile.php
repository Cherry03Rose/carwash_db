<?php
// Safely start the session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
}


// Check if the user is logged in
$user_logged_in = isset($_SESSION['user_name']) && isset($_SESSION['user_email']);

if ($user_logged_in) {
    $user_name = $_SESSION['user_name'];
    $user_email = $_SESSION['user_email'];

    if (!empty($_SESSION['user_photo'])) {
        $user_photo = $_SESSION['user_photo']; // Gmail photo
        $show_initials = false;
    } else {
        // Generate initials from full name
        $name_parts = preg_split('/\s+/', trim($user_name));
        $initials = '';
        foreach ($name_parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        $show_initials = true;
    }
}
?>

 <style>
        .profile-dropdown {
            position: relative;
            display: inline-block;
            margin-left: 35px; /* More space from button */
            vertical-align: middle;
        }
        .profile-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
            cursor: pointer;
            background: #eee;
            transition: box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .initials {
    display: inline-block;
        }
        .dropdown-menu-custom {
            display: none;
            position: absolute;
            right: 0;
            top: 120%;
            min-width: 220px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border-radius: 7px;
            padding: 20px 15px 10px 15px;
            z-index: 9999;
            text-align: left;
        }
        .dropdown-menu-custom.active {
            display: block;
            animation: fadeIn 0.18s;
        }
        @keyframes fadeIn {
          from {opacity: 0; transform: translateY(10px);}
          to {opacity: 1; transform: translateY(0);}
        }
        .dropdown-menu-custom .user-name {
            font-weight: 700;
            font-size: 1.05em;
            margin-bottom: 2px;
            white-space: nowrap;
        }
        .dropdown-menu-custom .user-email {
            font-size: 0.95em;
            color: #888;
            margin-bottom: 12px;
            word-break: break-all;
        }
        .dropdown-menu-custom .dropdown-divider {
            margin: 9px 0;
        }
        .dropdown-menu-custom .logout-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 7px 20px;
            border-radius: 4px;
            width: 100%;
            transition: background 0.2s;
            font-size: 1em;
        }
        .dropdown-menu-custom .logout-btn:hover {
            background: #bd2130;
        }
        @media (max-width: 991.98px) {
            .profile-dropdown {
                margin-left: 20px;
                margin-top: 10px;
            }
        }
        @media (max-width: 767.98px) {
            .profile-dropdown {
                margin-left: 0;
                margin-top: 10px;
            }
            .dropdown-menu-custom {
                left: auto;
                right: 0;
                min-width: 180px;
            }
        }
    </style>

<?php if($user_logged_in): ?>
    <!-- Profile Circle & Dropdown -->
    <div class="profile-dropdown" id="profileDropdown">
        <div class="profile-circle" id="profileCircle" tabindex="0">
            <?php if (!$show_initials): ?>
                <img src="<?php echo htmlspecialchars($user_photo); ?>" alt="Profile">
            <?php else: ?>
                <span class="initials"><?php echo $initials; ?></span>
            <?php endif; ?>
        </div>
        <div class="dropdown-menu-custom" id="profileDropdownMenu">
            <div class="user-name" id="profileName"><?php echo htmlspecialchars($user_name); ?></div>
            <div class="user-email" id="profileEmail"><?php echo htmlspecialchars($user_email); ?></div>
            <div class="dropdown-divider"></div>
            <button class="logout-btn" onclick="handleLogout()">Logout</button>
        </div>
    </div>
<?php endif; ?>

<script>
      // Toggle dropdown
      document.addEventListener('DOMContentLoaded', function() {
        var profileCircle = document.getElementById('profileCircle');
        var dropdownMenu = document.getElementById('profileDropdownMenu');
        var profileDropdown = document.getElementById('profileDropdown');
        if(!profileCircle) return;

        function closeDropdown(e) {
          if (
            profileDropdown && !profileDropdown.contains(e.target)
          ) {
            dropdownMenu.classList.remove('active');
          }
        }
        profileCircle.addEventListener('click', function(e) {
          dropdownMenu.classList.toggle('active');
          e.stopPropagation();
        });
        profileCircle.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            dropdownMenu.classList.toggle('active');
            e.preventDefault();
          }
        });
        document.addEventListener('click', closeDropdown);
        window.addEventListener('scroll', function() {
          dropdownMenu.classList.remove('active');
        });
      });
      function handleLogout() {
        window.location.href = "logout.php";
      }
    </script>
</body>
</html>