
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Common navbar for root, user and admin folders.
 * Links work on localhost and InfinityFree.
 */

$scriptDir = str_replace(
    "\\",
    "/",
    dirname($_SERVER["SCRIPT_NAME"] ?? "/")
);

$scriptDir = rtrim($scriptDir, "/");

if (
    preg_match("~/(user|admin)$~", $scriptDir)
) {
    $scriptDir = dirname($scriptDir);
    $scriptDir = str_replace("\\", "/", $scriptDir);
}

$baseUrl = rtrim($scriptDir, "/") . "/";

function sh_nav_url($path) {
    global $baseUrl;
    return htmlspecialchars(
        $baseUrl . $path,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<style>
.sh-navbar,
.sh-navbar * {
    box-sizing: border-box;
}

.sh-navbar {
    width: 100%;
    background: #111827;
    padding: 18px 7%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    font-family: Arial, sans-serif;
}

.sh-navbar .sh-logo {
    color: #fff;
    font-size: 28px;
    font-weight: bold;
    text-decoration: none;
    white-space: nowrap;
}

.sh-navbar .sh-logo span {
    color: #38bdf8;
}

.sh-navbar .sh-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 15px 22px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.sh-navbar .sh-links a {
    display: inline-block;
    color: white;
    text-decoration: none;
    font-size: 15px;
    line-height: 1.4;
}

.sh-navbar .sh-links a:hover {
    color: #38bdf8;
}

.sh-navbar .sh-links .sh-login-btn {
    border: 1px solid #38bdf8;
    border-radius: 7px;
    padding: 9px 16px;
}

.sh-navbar .sh-links .sh-admin-btn {
    border: 1px solid #94a3b8;
    border-radius: 7px;
    padding: 9px 16px;
}

@media (max-width: 900px) {
    .sh-navbar {
        padding: 17px 5%;
        flex-direction: column;
        gap: 18px;
    }

    .sh-navbar .sh-logo {
        font-size: 27px;
    }

    .sh-navbar .sh-links {
        width: 100%;
        gap: 13px 18px;
    }
}

@media (max-width: 480px) {
    .sh-navbar {
        padding: 16px 12px;
    }

    .sh-navbar .sh-logo {
        font-size: 26px;
    }

    .sh-navbar .sh-links {
        gap: 12px 15px;
    }

    .sh-navbar .sh-links a {
        font-size: 14px;
    }

    .sh-navbar .sh-links .sh-login-btn,
    .sh-navbar .sh-links .sh-admin-btn {
        padding: 8px 12px;
    }
}
</style>

<nav class="sh-navbar" aria-label="Main navigation">

    <a href="<?php echo sh_nav_url('index.php'); ?>" class="sh-logo">
        Service<span>Hub</span>
    </a>

    <ul class="sh-links">

        <li>
            <a href="<?php echo sh_nav_url('index.php'); ?>">Home</a>
        </li>

        <li>
            <a href="<?php echo sh_nav_url('services.php'); ?>">Services</a>
        </li>

        <li>
            <a href="<?php echo sh_nav_url('about.php'); ?>">About</a>
        </li>

        <li>
            <a href="<?php echo sh_nav_url('index.php#how-it-works'); ?>">How It Works</a>
        </li>

        <li>
            <a href="<?php echo sh_nav_url('contact.php'); ?>">Contact</a>
        </li>

        <?php if (isset($_SESSION["user_id"])): ?>

            <li>
                <a href="<?php echo sh_nav_url('user/dashboard.php'); ?>">
                    Dashboard
                </a>
            </li>

            <li>
                <a
                    href="<?php echo sh_nav_url('includes/logout.php'); ?>"
                    class="sh-login-btn"
                >
                    Logout
                </a>
            </li>

        <?php else: ?>

            <li>
                <a
                    href="<?php echo sh_nav_url('user/login.php'); ?>"
                    class="sh-login-btn"
                >
                    Login
                </a>
            </li>

            <li>
                <a href="<?php echo sh_nav_url('user/register.php'); ?>">
                    Register
                </a>
            </li>

        <?php endif; ?>

        <li>
            <a
                href="<?php echo sh_nav_url('admin/admin_login.php'); ?>"
                class="sh-admin-btn"
            >
                Admin Login
            </a>
        </li>

    </ul>

</nav>
