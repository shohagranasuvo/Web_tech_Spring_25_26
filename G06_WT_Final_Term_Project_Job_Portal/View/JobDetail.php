<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seeker") {
    Header("Location: login.php");
    exit();
}

include "../Model/db.php";
$database = new db();
$connection = $database->connection();

// Get job ID from URL
$job_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($job_id)) {
    Header("Location: JobBoard.php");
    exit();
}

$user_id = $_SESSION["user_id"];


$job_result = $database->getJobById($connection, $job_id);

if (!$job_result || $job_result->num_rows === 0) {
    Header("Location: JobBoard.php");
    exit();
}

$job = $job_result->fetch_assoc();

// Check if already applied
$has_applied = $database->hasApplied($connection, $user_id, $job_id);

// Check if saved
$is_saved = $database->isSavedJob($connection, $user_id, $job_id);


$seeker_result = $database->getSeekerProfile($connection, $user_id);
$seeker = $seeker_result->fetch_assoc();
$user = $database->getUserById($connection, $user_id)->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($job['title']); ?> - Job Details</title>
    <style>
        body { 
            font-family: Arial; 
            background: #f0f4ff; 
            margin: 0; 
            padding: 20px; 
        }
        .header {
            background: white;
            padding: 15px 30px;
            border: 1px solid #1a1aff;
            margin-bottom: 20px;
        }
        .header a { 
            color: #1a1aff; 
            text-decoration: none; 
            font-weight: bold; 
        }
        .container { max-width: 900px; margin: 0 auto; }
        
        .job-header {
            background: white;
            border: 1px solid #1a1aff;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .job-title-main {
            font-size: 26px;
            font-weight: bold;
            color: #1a1aff;
            margin-bottom: 15px;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ddd;
        }
        
        .company-details h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .company-details p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .bookmark-btn-large {
            background: none;
            border: 2px solid #1a1aff;
            padding: 8px 18px;
            cursor: pointer;
            font-size: 16px;
            color: #1a1aff;
            transition: all 0.3s;
        }
        
        .bookmark-btn-large.saved {
            background: #ff1a1a;
            color: white;
            border-color: #ff1a1a;
        }
        
        .bookmark-btn-large:hover {
            background: #1a1aff;
            color: white;
        }
        
        .job-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            margin-top: 15px;
        }
        
        .meta-item {
            font-size: 13px;
        }
        
        .meta-label {
            color: #666;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 3px;
        }
        
        .meta-value {
            color: #333;
            font-weight: bold;
        }
        
        .section {
            background: white;
            border: 1px solid #1a1aff;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1aff;
            margin-bottom: 12px;
            border-bottom: 2px solid #1a1aff;
            padding-bottom: 8px;
        }
        
        .job-description {
            line-height: 1.7;
            color: #555;
            white-space: pre-wrap;
        }
        
        .apply-section {
            background: #fffacd;
            border: 2px solid #1a1aff;
            padding: 25px;
            text-align: center;
        }
        
        .apply-btn {
            padding: 12px 35px;
            background: #1a1aff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        
        .apply-btn:hover {
            background: #0000cc;
        }
        
        .applied-badge {
            display: inline-block;
            padding: 12px 35px;
            background: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }
        
        .application-form {
            max-width: 650px;
            margin: 25px auto;
            background: white;
            border: 1px solid #1a1aff;
            padding: 25px;
            display: none;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #1a1aff;
            font-family: Arial;
            resize: vertical;
            min-height: 100px;
            box-sizing: border-box;
        }
        
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #1a1aff;
        }
        
        .resume-option {
            margin-bottom: 12px;
        }
        
        .resume-option label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: normal;
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #1a1aff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
        }
        
        .submit-btn:hover {
            background: #0000cc;
        }
        
        .cancel-btn {
            width: 100%;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 15px;
            margin-top: 8px;
        }
        
        .cancel-btn:hover {
            background: #5a6268;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

<div class="header">
    <a href="JobBoard.php">← Back to Job Board</a>
</div>

<div class="container">
    <?php
    $formError = $_GET['error'] ?? '';
    $success = $_GET['success'] ?? '';
    if ($formError) echo "<div class='error'>" . htmlspecialchars($formError) . "</div>";
    if ($success) echo "<div class='success'>" . htmlspecialchars($success) . "</div>";
    ?>
    
    <div class="job-header">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div style="flex: 1;">
                <h1 class="job-title-main"><?php echo htmlspecialchars($job['title']); ?></h1>
                
                <div class="company-info">
                    <?php if (!empty($job['company_logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($job['company_logo']); ?>" 
                             alt="Logo" class="company-logo">
                    <?php endif; ?>
                    <div class="company-details">
                        <h3><?php echo htmlspecialchars($job['company_name']); ?></h3>
                        <p><?php echo htmlspecialchars($job['industry']); ?></p>
                        <?php if (!empty($job['website'])): ?>
                            <p><a href="<?php echo htmlspecialchars($job['website']); ?>" 
                                  target="_blank">Visit Website</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <button class="bookmark-btn-large <?php echo $is_saved ? 'saved' : ''; ?>" 
                    id="bookmark-btn"
                    data-job-id="<?php echo $job['id']; ?>">
                <?php echo $is_saved ? '♥ Saved' : '♡ Save'; ?>
            </button>
        </div>
        
        <div class="job-meta">
            <div class="meta-item">
                <div class="meta-label">Category</div>
                <div class="meta-value"><?php echo htmlspecialchars($job['category_name']); ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Type</div>
                <div class="meta-value"><?php echo htmlspecialchars($job['job_type']); ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Location</div>
                <div class="meta-value"><?php echo htmlspecialchars($job['location']); ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Salary</div>
                <div class="meta-value"><?php echo htmlspecialchars($job['salary_range']); ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Deadline</div>
                <div class="meta-value"><?php echo date('M d, Y', strtotime($job['deadline'])); ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Posted</div>
                <div class="meta-value"><?php echo date('M d, Y', strtotime($job['created_at'])); ?></div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h2 class="section-title">Job Description</h2>
        <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
    </div>
    
    <div class="section">
        <h2 class="section-title">Requirements</h2>
        <div class="job-description"><?php echo htmlspecialchars($job['requirements']); ?></div>
    </div>
    
    <?php if (!empty($job['company_description'])): ?>
    <div class="section">
        <h2 class="section-title">About the Company</h2>
        <div class="job-description"><?php echo htmlspecialchars($job['company_description']); ?></div>
    </div>
    <?php endif; ?>
    
    <div class="apply-section" id="apply-section">
        <?php if ($has_applied): ?>
            <div class="applied-badge">✓ Application Submitted</div>
            <p style="margin-top: 10px;">You have already applied for this position.</p>
            <p><a href="../Controller/MyApplicationsController.php">View My Applications</a></p>
        <?php else: ?>
            <h2 style="margin-bottom: 12px;">Ready to Apply?</h2>
            <p style="margin-bottom: 18px;">Submit your application now!</p>
            <button class="apply-btn" id="show-application-form">Apply Now</button>
            
            <?php
            $days_left = (strtotime($job['deadline']) - time()) / (60 * 60 * 24);
            if ($days_left < 7): ?>
                <p style="color: #e74c3c; margin-top: 10px;">
                    ⚠ Only <?php echo ceil($days_left); ?> days left!
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
  
    <div class="application-form" id="application-form">
        <h2 class="section-title">Submit Your Application</h2>
        
        <form action="../Controller/ApplyJobController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
            
            <div class="form-group">
                <label>Cover Letter *</label>
                <textarea name="cover_letter" 
                          required
                          placeholder="Explain why you're a great fit for this position..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Resume *</label>
                
                <?php if (!empty($user['file_path'])): ?>
                <div class="resume-option">
                    <label>
                        <input type="radio" name="resume_option" value="profile" checked>
                        Use my profile resume
                        <span style="color: #666; font-size: 12px;">
                            (<?php echo basename($user['file_path']); ?>)
                        </span>
                    </label>
                </div>
                
                <div class="resume-option">
                    <label>
                        <input type="radio" name="resume_option" value="upload">
                        Upload a new resume
                    </label>
                </div>
                <?php endif; ?>
                
                <input type="file" 
                       id="resume-upload" 
                       name="resume" 
                       accept=".pdf,.doc,.docx"
                       <?php echo empty($user['file_path']) ? 'required' : ''; ?>
                       <?php echo !empty($user['file_path']) ? 'disabled' : ''; ?>>
                <small style="color: #666;">Accepted: PDF, DOC, DOCX (Max 2MB)</small>
            </div>
            
            <button type="submit" class="submit-btn">Submit Application</button>
            <button type="button" class="cancel-btn" id="cancel-application">Cancel</button>
        </form>
    </div>
</div>

<script>

    const bookmarkBtn = document.getElementById('bookmark-btn');
    
    bookmarkBtn.addEventListener('click', function() {
        const jobId = this.getAttribute('data-job-id');
        
        fetch('../Controller/ToggleSavedJobController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ job_id: jobId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    this.classList.add('saved');
                    this.innerHTML = '♥ Saved';
                } else {
                    this.classList.remove('saved');
                    this.innerHTML = '♡ Save';
                }
            } else {
                alert(data.message || 'Failed to save job');
            }
        });
    });
    
   
    const showFormBtn = document.getElementById('show-application-form');
    const applicationForm = document.getElementById('application-form');
    const cancelBtn = document.getElementById('cancel-application');
    const applySection = document.getElementById('apply-section');
    
    if (showFormBtn) {
        showFormBtn.addEventListener('click', function() {
            applicationForm.style.display = 'block';
            applySection.style.display = 'none';
            applicationForm.scrollIntoView({ behavior: 'smooth' });
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            applicationForm.style.display = 'none';
            applySection.style.display = 'block';
        });
    }

    const resumeOptions = document.querySelectorAll('input[name="resume_option"]');
    const resumeUpload = document.getElementById('resume-upload');
    
    resumeOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.value === 'upload') {
                resumeUpload.disabled = false;
                resumeUpload.required = true;
            } else {
                resumeUpload.disabled = true;
                resumeUpload.required = false;
                resumeUpload.value = '';
            }
        });
    });
</script>

</body>
</html>
<?php $connection->close(); ?>
