// script.js - Main JavaScript File

// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar')) {
            navMenu.classList.remove('active');
        }
    });
    
    // Active navigation link
    setActiveNavLink();
    
    // Initialize project filtering
    initProjectFilters();
    
    // Initialize skill bars animation
    animateSkillBars();
    
    // Initialize smooth scrolling
    initSmoothScroll();
});

// Set active navigation link
function setActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
            link.classList.add('active');
        }
    });
}

// Project filtering
function initProjectFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');
    
    if (filterButtons.length === 0) return;
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter projects
            projectCards.forEach(card => {
                const projectType = card.getAttribute('data-type');
                
                if (filter === 'all' || projectType === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Animate skill bars on scroll
function animateSkillBars() {
    const skillBars = document.querySelectorAll('.skill-progress');
    
    if (skillBars.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const progress = entry.target;
                const width = progress.getAttribute('data-width');
                progress.style.width = width + '%';
            }
        });
    }, { threshold: 0.5 });
    
    skillBars.forEach(bar => observer.observe(bar));
}

// Smooth scrolling
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Modal Functions
let currentModal = null;

function openModal(projectId, type) {
    const modal = document.getElementById('projectModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (!modal) return;
    
    currentModal = modal;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Show loading spinner
    modalBody.innerHTML = '<div class="spinner"></div>';
    
    // Fetch project details via AJAX
    fetch(`get_project.php?id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            modalTitle.textContent = data.title;
            
            let content = '';
            
            switch(data.project_type) {
                case 'image':
                    content = `<img src="${data.file_path}" alt="${data.title}" class="modal-image">`;
                    break;
                    
                case 'pdf':
                    content = `<iframe src="${data.file_path}" class="modal-iframe"></iframe>`;
                    break;
                    
                case 'html':
                    content = `<iframe src="${data.file_path}" class="modal-iframe"></iframe>`;
                    break;
                    
                case 'powerbi':
                case 'dashboard':
                    content = `<iframe src="${data.external_link}" class="modal-iframe" allowfullscreen></iframe>`;
                    break;
                    
                case 'github':
                    window.open(data.external_link, '_blank');
                    closeModal();
                    return;
            }
            
            modalBody.innerHTML = `
                <div class="project-modal-content">
                    ${content}
                    <div style="margin-top: 2rem;">
                        <h3>Description</h3>
                        <p>${data.description}</p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            modalBody.innerHTML = '<p style="color: red;">Error loading project details.</p>';
            console.error('Error:', error);
        });
}

function closeModal() {
    if (currentModal) {
        currentModal.classList.remove('active');
        document.body.style.overflow = 'auto';
        currentModal = null;
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && currentModal) {
        closeModal();
    }
});

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });
    
    return isValid;
}

// File Upload Preview
function previewFile(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    
    if (file && preview) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 200px;">`;
            } else {
                preview.innerHTML = `<p>File selected: ${file.name}</p>`;
            }
        };
        
        reader.readAsDataURL(file);
    }
}

// Delete confirmation
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideInRight 0.3s ease';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// AJAX form submission helper
function submitFormAjax(formId, successCallback) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm(formId)) {
            showNotification('Please fill in all required fields.', 'error');
            return;
        }
        
        const formData = new FormData(form);
        const action = form.getAttribute('action');
        
        fetch(action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                if (successCallback) successCallback(data);
                form.reset();
            } else {
                showNotification(data.message || 'An error occurred.', 'error');
            }
        })
        .catch(error => {
            showNotification('Network error. Please try again.', 'error');
            console.error('Error:', error);
        });
    });
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    }).catch(err => {
        showNotification('Failed to copy.', 'error');
    });
}

// Add CSS for slide animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);