<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<style>
    .job-detail-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px 40px 20px;
    }
    
    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #007bff;
        text-decoration: none;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
    
    .job-header-section {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .job-header-top {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 20px;
    }
    
    .job-title-large {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }
    
    .company-info {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .company-logo {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
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
        border: 2px solid #ddd;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s;
    }
    
    .bookmark-btn-large.saved {
        border-color: #e74c3c;
        color: #e74c3c;
    }
    
    .bookmark-btn-large:hover {
        border-color: #e74c3c;
        color: #e74c3c;
    }
    
    .job-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .meta-item {
        display: flex;
        flex-direction: column;
    }
    
    .meta-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    
    .meta-value {
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }
    
    .job-section {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    
    .job-description {
        line-height: 1.8;
        color: #555;
    }
    
    .apply-section {
        background: #f8f9fa;
        border: 2px solid #007bff;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
    }
    
    .apply-btn {
        padding: 15px 40px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .apply-btn:hover {
        background: #218838;
    }
    
    .applied-badge {
        display: inline-block;
        padding: 15px 40px;
        background: #28a745;
        color: white;
        border-radius: 5px;
        font-size: 18px;
        font-weight: bold;
    }
    
    .application-form {
        max-width: 700px;
        margin: 30px auto;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 30px;
        display: none;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
    }
    
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
        min-height: 120px;
    }
    
    .form-group input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    
    .resume-option {
        margin-bottom: 15px;
    }
    
    .resume-option label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }
    
    .submit-btn {
        width: 100%;
        padding: 15px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .submit-btn:hover {
        background: #0056b3;
    }
    
    .cancel-btn {
        width: 100%;
        padding: 15px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 10px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .cancel-btn:hover {
        background: #5a6268;
    }
</style>

<div class="job-detail-container">
    <a href="<?php echo BASE_URL; ?>index.php?page=jobs" class="back-link">← Back to Job Board</a>
    
    <div class="job-header-section">
        <div class="job-header-top">
            <div style="flex: 1;">
                <h1 class="job-title-large"><?php echo htmlspecialchars($job['title']); ?></h1>
                
                <div class="company-info">
                    <?php if (!empty($job['company_logo'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($job['company_logo']); ?>" 
                             alt="Company Logo" class="company-logo">
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
                <?php echo $is_saved ? '♥ Saved' : '♡ Save Job'; ?>
            </button>
        </div>
        
        <div class="job-meta">
            <div class="meta-item">
                <span class="meta-label">Category</span>
                <span class="meta-value"><?php echo htmlspecialchars($job['category_name']); ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Job Type</span>
                <span class="meta-value"><?php echo htmlspecialchars($job['job_type']); ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Location</span>
                <span class="meta-value"><?php echo htmlspecialchars($job['location']); ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Salary Range</span>
                <span class="meta-value"><?php echo htmlspecialchars($job['salary_range']); ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Application Deadline</span>
                <span class="meta-value"><?php echo date('M d, Y', strtotime($job['deadline'])); ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Posted On</span>
                <span class="meta-value"><?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
            </div>
        </div>
    </div>
    
    <div class="job-section">
        <h2 class="section-title">Job Description</h2>
        <div class="job-description">
            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
        </div>
    </div>
    
    <div class="job-section">
        <h2 class="section-title">Requirements</h2>
        <div class="job-description">
            <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
        </div>
    </div>
    
    <?php if (!empty($job['company_description'])): ?>
    <div class="job-section">
        <h2 class="section-title">About the Company</h2>
        <div class="job-description">
            <?php echo nl2br(htmlspecialchars($job['company_description'])); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="apply-section" id="apply-section">
        <?php if ($has_applied): ?>
            <div class="applied-badge">✓ Application Submitted</div>
            <p style="margin-top: 10px;">You have already applied for this position.</p>
            <p><a href="<?php echo BASE_URL; ?>index.php?page=applications">View My Applications</a></p>
        <?php else: ?>
            <h2 style="margin-bottom: 15px;">Ready to Apply?</h2>
            <p style="margin-bottom: 20px;">Submit your application and take the next step in your career!</p>
            <button class="apply-btn" id="show-application-form">Apply Now</button>
            
            <?php
            $days_left = (strtotime($job['deadline']) - time()) / (60 * 60 * 24);
            if ($days_left < 7): ?>
                <p style="color: #e74c3c; margin-top: 10px;">
                    ⚠ Only <?php echo ceil($days_left); ?> days left to apply!
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Application Form -->
    <div class="application-form" id="application-form">
        <h2 class="section-title">Submit Your Application</h2>
        
        <form action="<?php echo BASE_URL; ?>index.php?page=apply-submit" 
              method="POST" 
              enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
            
            <div class="form-group">
                <label for="cover-letter">Cover Letter *</label>
                <textarea id="cover-letter" 
                          name="cover_letter" 
                          required
                          placeholder="Tell us why you're a great fit for this position..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Resume *</label>
                
                <?php if (!empty($seeker['file_path'])): ?>
                <div class="resume-option">
                    <label>
                        <input type="radio" name="resume_option" value="profile" checked>
                        Use my profile resume
                        <span style="color: #666; font-size: 13px;">
                            (<?php echo basename($seeker['file_path']); ?>)
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
                       <?php echo empty($seeker['file_path']) ? 'required' : ''; ?>
                       <?php echo !empty($seeker['file_path']) ? 'disabled' : ''; ?>>
                <small style="color: #666; display: block; margin-top: 5px;">
                    Accepted formats: PDF, DOC, DOCX (Max 5MB)
                </small>
            </div>
            
            <button type="submit" class="submit-btn">Submit Application</button>
            <button type="button" class="cancel-btn" id="cancel-application">Cancel</button>
        </form>
    </div>
</div>

<script>
    // Bookmark functionality
    const bookmarkBtn = document.getElementById('bookmark-btn');
    
    bookmarkBtn.addEventListener('click', function() {
        const jobId = this.getAttribute('data-job-id');
        
        fetch('<?php echo BASE_URL; ?>index.php?page=toggle-saved', {
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
                    this.innerHTML = '♡ Save Job';
                }
            } else {
                alert(data.message || 'Failed to save job');
            }
        })
        .catch(error => {
            console.error('Bookmark error:', error);
            alert('Failed to save job. Please try again.');
        });
    });
    
    // Show/hide application form
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
            applySection.scrollIntoView({ behavior: 'smooth' });
        });
    }
    
    // Resume option toggle
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

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
