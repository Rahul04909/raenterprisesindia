<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); } 
$rootPath = isset($rootPath) ? $rootPath : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RA Enterprises India</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/bulk-quote.css">
</head>
<body>

<header class="main-header">
    <nav class="navbar">
        <!-- Logo Section -->
        <div class="logo-container">
            <a href="<?php echo $rootPath; ?>index.php" class="brand-logo">
                <img src="<?php echo $rootPath; ?>assets/logo/logo.png" alt="RA Enterprises" onerror="this.src='https://placehold.co/150x50?text=RA+Enterprises'">
            </a>
            

        </div>

        <!-- Search Bar -->
        <div class="search-container d-md-flex">
            <input type="text" class="search-input" placeholder="Search Product, Category, Brand...">
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <!-- Nav Actions -->
        <div class="nav-actions">
            <a href="javascript:void(0)" class="nav-item get-quote-btn" onclick="openBulkModal()">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Get Bulk Quote</span>
            </a>
            
            <a href="https://wa.me/919999049135" class="nav-item whatsapp-btn" target="_blank">
                <i class="fa-brands fa-whatsapp"></i>
                <span>+91 9999049135</span>
            </a>
            
             <div class="nav-item mobile-toggle">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </div>
        </div>
    </nav>

    <!-- Mobile Search Bar (Visible on Mobile Only) -->
    <div class="mobile-search-bar">
         <div class="search-container" style="display:flex; margin:0; width:100%;">
            <input type="text" class="search-input" placeholder="Search Product, Category, Brand...">
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
</header>

<!-- Bulk Quote Modal -->
<div id="bulkQuoteModal" class="bulk-quote-modal">
    <div class="bulk-modal-content">
        <span class="bulk-close" onclick="closeBulkModal()">&times;</span>
        
        <div class="bulk-modal-header">
            <h2>Request a Bulk Quote</h2>
            <p>Fill out the form below and we'll get back to you with the best prices.</p>
        </div>

        <div id="bulkSuccessMessage" class="bulk-success-message" style="display:none;">
            <div class="bulk-success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h3>Request Sent Successfully!</h3>
            <p>Our team will review your requirements and contact you shortly.</p>
            <button class="bulk-submit-btn" onclick="closeBulkModal()" style="margin-top:20px;">Close</button>
        </div>

        <form id="bulkQuoteForm" onsubmit="handleBulkSubmit(event)" enctype="multipart/form-data">
            <div class="bulk-form-grid">
                <div class="bulk-form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" placeholder="E.g. John Doe" required>
                </div>
                <div class="bulk-form-group">
                    <label>Company Name (Optional)</label>
                    <input type="text" name="company_name" placeholder="Your Company Ltd.">
                </div>
                <div class="bulk-form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="bulk-form-group">
                    <label>Mobile Number *</label>
                    <input type="tel" name="mobile" placeholder="10-digit number" pattern="[0-9]{10}" required>
                </div>
                <div class="bulk-form-group full-width">
                    <label>Complete Address *</label>
                    <textarea name="address" rows="2" placeholder="Street, Area, City, Pincode" required></textarea>
                </div>
                <div class="bulk-form-group full-width">
                    <label>Requirements Attachment (PDF, Excel, Word, Image)</label>
                    <div class="bulk-file-upload" onclick="document.getElementById('bulkFile').click()">
                        <input type="file" id="bulkFile" name="attachment" style="display:none;" onchange="updateFileName(this)">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span id="fileNameDisplay">Click to upload or drag and drop</span>
                        <small style="color:#a0aec0; font-size:11px;">Max size: 10MB</small>
                    </div>
                </div>
            </div>
            <button type="submit" class="bulk-submit-btn" id="bulkSubmitBtn">Send Bulk Quote Request</button>
        </form>
    </div>
</div>

<script>
function openBulkModal() {
    document.getElementById('bulkQuoteModal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent scroll
}

function closeBulkModal() {
    document.getElementById('bulkQuoteModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    // Reset form after a delay if success was shown
    setTimeout(() => {
        document.getElementById('bulkQuoteForm').style.display = 'block';
        document.getElementById('bulkSuccessMessage').style.display = 'none';
        document.getElementById('bulkQuoteForm').reset();
        document.getElementById('fileNameDisplay').innerText = 'Click to upload or drag and drop';
    }, 500);
}

function updateFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileNameDisplay').innerText = 'Selected: ' + input.files[0].name;
    }
}

async function handleBulkSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('bulkSubmitBtn');
    const form = e.target;
    const formData = new FormData(form);

    btn.disabled = true;
    btn.innerText = 'Sending...';

    try {
        const response = await fetch('<?php echo $rootPath; ?>handlers/bulk-quote-handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            form.style.display = 'none';
            document.getElementById('bulkSuccessMessage').style.display = 'block';
        } else {
            alert(result.message || 'Error sending request. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Connectivity issue. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Send Bulk Quote Request';
    }
}

// Close when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('bulkQuoteModal');
    if (event.target == modal) {
        closeBulkModal();
    }
}
</script>
</header>


<!-- Main Content Wrapper Starts Here -->
<main>
