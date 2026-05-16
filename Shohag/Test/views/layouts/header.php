<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }
        
        .navbar-menu {
            display: flex;
            gap: 25px;
            list-style: none;
            align-items: center;
        }
        
        .navbar-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .navbar-menu a:hover,
        .navbar-menu a.active {
            color: #007bff;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-name {
            font-weight: 500;
        }
        
        .logout-btn {
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .flash-message {
            max-width: 1200px;
            margin: -10px auto 20px auto;
            padding: 15px 20px;
            border-radius: 5px;
        }
        
        .flash-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .flash-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <div class="navbar-container">
            <a href="<?php echo BASE_URL; ?>index.php" class="navbar-brand"><?php echo APP_NAME; ?></a>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'seeker'): ?>
            <ul class="navbar-menu">
                <li><a href="<?php echo BASE_URL; ?>index.php?page=jobs" 
                       class="<?php echo (!isset($_GET['page']) || $_GET['page'] === 'jobs') ? 'active' : ''; ?>">
                    Browse Jobs
                </a></li>
                <li><a href="<?php echo BASE_URL; ?>index.php?page=saved-jobs"
                       class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'saved-jobs') ? 'active' : ''; ?>">
                    Saved Jobs
                </a></li>
                <li><a href="<?php echo BASE_URL; ?>index.php?page=applications"
                       class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'applications') ? 'active' : ''; ?>">
                    My Applications
                </a></li>
            </ul>
            
            <div class="navbar-user">
                <span class="user-name">
                    Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
                </span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    
    <?php if (isset($success) && !empty($success)): ?>
        <div class="flash-message flash-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error) && !empty($error)): ?>
        <div class="flash-message flash-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
