<?php
// Mock Team Data (Can be fetched from DB in future)
$team_members = [
    [
        'name' => 'Rahul Sharma',
        'designation' => 'Founder & CEO',
        'image' => 'https://i.pravatar.cc/150?u=rahul'
    ],
    [
        'name' => 'Priya Gupta',
        'designation' => 'Operations Manager',
        'image' => 'https://i.pravatar.cc/150?u=priya'
    ],
    [
        'name' => 'Amit Verma',
        'designation' => 'Technical Lead',
        'image' => 'https://i.pravatar.cc/150?u=amit'
    ],
    [
        'name' => 'Sneha Reddy',
        'designation' => 'Customer Relations',
        'image' => 'https://i.pravatar.cc/150?u=sneha'
    ],
    [
        'name' => 'Vikram Singh',
        'designation' => 'Sales Director',
        'image' => 'https://i.pravatar.cc/150?u=vikram'
    ],
    [
        'name' => 'Anjali Mehta',
        'designation' => 'Product Designer',
        'image' => 'https://i.pravatar.cc/150?u=anjali'
    ]
];
?>

<div class="team-section">
    <div class="team-container">
        <div class="team-header">
            <h2 class="team-title">Our Expert Team</h2>
            <p class="team-subtitle">Meet the dedicated professionals behind RA Enterprises</p>
        </div>

        <div class="team-slider-wrapper">
            <button class="team-nav-btn prev-btn" onclick="slideTeam(-1)">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            
            <div class="team-slider" id="teamSlider">
                <?php foreach ($team_members as $member): ?>
                    <div class="team-card">
                        <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="team-member-img">
                        <h3 class="team-member-name"><?php echo $member['name']; ?></h3>
                        <p class="team-member-designation"><?php echo $member['designation']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="team-nav-btn next-btn" onclick="slideTeam(1)">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('teamSlider');
    let isDown = false;
    let startX;
    let scrollLeft;
    let autoplayInterval;

    // Slide function
    window.slideTeam = function(direction) {
        const scrollAmount = slider.offsetWidth * 0.8;
        slider.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
        resetAutoplay();
    };

    // Autoplay logic
    function startAutoplay() {
        autoplayInterval = setInterval(() => {
            if (slider.scrollLeft + slider.offsetWidth >= slider.scrollWidth - 10) {
                slider.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                slider.scrollBy({ left: 300, behavior: 'smooth' });
            }
        }, 4000);
    }

    function resetAutoplay() {
        clearInterval(autoplayInterval);
        startAutoplay();
    }

    startAutoplay();

    // Mouse drag to scroll functionality
    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.classList.add('active');
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
        clearInterval(autoplayInterval);
    });

    slider.addEventListener('mouseleave', () => {
        isDown = false;
        slider.classList.remove('active');
        startAutoplay();
    });

    slider.addEventListener('mouseup', () => {
        isDown = false;
        slider.classList.remove('active');
        startAutoplay();
    });

    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2;
        slider.scrollLeft = scrollLeft - walk;
    });
});
</script>
