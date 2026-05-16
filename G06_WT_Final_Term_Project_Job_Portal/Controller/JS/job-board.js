
document.addEventListener('DOMContentLoaded', function() {
    
    const searchBox = document.getElementById('search-box');
    const categoryFilter = document.getElementById('category-filter');
    const typeFilter = document.getElementById('type-filter');
    const locationFilter = document.getElementById('location-filter');
    const salaryFilter = document.getElementById('salary-filter');
    const jobsContainer = document.getElementById('jobs-container');
    const resultsCount = document.getElementById('results-count');
    
    let searchTimeout;
    
   
    searchBox.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length === 0) {
       
            loadJobsWithFilters();
            return;
        }
        
      
        searchTimeout = setTimeout(function() {
            searchJobs(query);
        }, 300);
    });
    

    categoryFilter.addEventListener('change', loadJobsWithFilters);
    typeFilter.addEventListener('change', loadJobsWithFilters);
    locationFilter.addEventListener('change', loadJobsWithFilters);
    salaryFilter.addEventListener('change', loadJobsWithFilters);

    function searchJobs(query) {
        jobsContainer.innerHTML = '<div class="loading">Searching...</div>';
        resultsCount.textContent = 'Searching...';
        
        fetch(`../Controller/SearchJobsController.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderJobs(data.jobs);
                    resultsCount.textContent = `Found ${data.count} job${data.count !== 1 ? 's' : ''} for "${query}"`;
                } else {
                    jobsContainer.innerHTML = '<div class="no-jobs">Error loading jobs</div>';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                jobsContainer.innerHTML = '<div class="no-jobs">Error loading jobs</div>';
            });
    }
    

    function loadJobsWithFilters() {
        const filters = {
            category_id: categoryFilter.value,
            job_type: typeFilter.value,
            location: locationFilter.value,
            salary_range: salaryFilter.value
        };

        const queryParams = new URLSearchParams();
        for (const [key, value] of Object.entries(filters)) {
            if (value) {
                queryParams.append(key, value);
            }
        }
        
        jobsContainer.innerHTML = '<div class="loading">Loading jobs...</div>';
        resultsCount.textContent = 'Loading...';
        
        fetch(`../Controller/FilterJobsController.php?${queryParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderJobs(data.jobs);
                    
                    const filterCount = Object.values(filters).filter(v => v).length;
                    const filterText = filterCount > 0 ? ` (${filterCount} filter${filterCount !== 1 ? 's' : ''})` : '';
                    resultsCount.textContent = `Showing ${data.count} job${data.count !== 1 ? 's' : ''}${filterText}`;
                } else {
                    jobsContainer.innerHTML = '<div class="no-jobs">Error loading jobs</div>';
                }
            })
            .catch(error => {
                console.error('Filter error:', error);
                jobsContainer.innerHTML = '<div class="no-jobs">Error loading jobs</div>';
            });
    }
    

    function renderJobs(jobs) {
        if (jobs.length === 0) {
            jobsContainer.innerHTML = '<div class="no-jobs">No jobs found matching your criteria.</div>';
            return;
        }
        
        jobsContainer.innerHTML = '';
        
        jobs.forEach(job => {
            const jobCard = createJobCard(job);
            jobsContainer.appendChild(jobCard);
        });
        

        attachBookmarkListeners();
    }

    function createJobCard(job) {
        const card = document.createElement('div');
        card.className = 'job-card';
        
        const deadlineDate = new Date(job.deadline);
        const formattedDeadline = deadlineDate.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        
        card.innerHTML = `
            <div class="job-header">
                <div style="flex: 1;">
                    <div class="job-title">${escapeHtml(job.title)}</div>
                    <div class="company-name">${escapeHtml(job.company_name)}</div>
                </div>
                <button class="bookmark-btn ${job.is_saved ? 'saved' : ''}" 
                        data-job-id="${job.id}">
                    ${job.is_saved ? '♥' : '♡'}
                </button>
            </div>
            
            <div class="job-badges">
                <span class="badge">${escapeHtml(job.category_name)}</span>
                <span class="badge">${escapeHtml(job.job_type)}</span>
            </div>
            
            <div class="job-details">
                <div><strong>Location:</strong> ${escapeHtml(job.location)}</div>
                <div><strong>Salary:</strong> ${escapeHtml(job.salary_range)}</div>
                <div><strong>Deadline:</strong> ${formattedDeadline}</div>
            </div>
            
            <a href="JobDetail.php?id=${job.id}" class="view-btn">
                View Details & Apply
            </a>
        `;
        
        return card;
    }
    

    function attachBookmarkListeners() {
        const bookmarkButtons = document.querySelectorAll('.bookmark-btn');
        
        bookmarkButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSavedJob(this);
            });
        });
    }

    function toggleSavedJob(button) {
        const jobId = button.getAttribute('data-job-id');
        
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
                // Toggle button state
                if (data.action === 'added') {
                    button.classList.add('saved');
                    button.innerHTML = '♥';
                } else {
                    button.classList.remove('saved');
                    button.innerHTML = '♡';
                }
            } else {
                alert(data.message || 'Failed to save job');
            }
        })
        .catch(error => {
            console.error('Bookmark error:', error);
            alert('Failed to save job. Please try again.');
        });
    }
    

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    

    attachBookmarkListeners();
    

    const initialJobCount = document.querySelectorAll('.job-card').length;
    if (initialJobCount > 0) {
        resultsCount.textContent = `Showing ${initialJobCount} job${initialJobCount !== 1 ? 's' : ''}`;
    }
});
