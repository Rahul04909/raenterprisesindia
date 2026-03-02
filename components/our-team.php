<?php
// Mock Team Data (Can be fetched from DB in future)
$team_members = [
    [
        'name' => 'Mr. Arjun Kumar',
        'designation' => 'Founder & CEO',
        'image' => 'assets/team-members/arjun-kumar.jpeg'
    ],
    [
        'name' => 'Ansh Kumar',
        'designation' => 'Operations Manager',
        'image' => 'assets/team-members/ansh-kumar.jpeg'
    ],
    [
        'name' => 'Kushal Veer',
        'designation' => 'Technical Lead',
        'image' => 'assets/team-members/kushal-veer.jpeg'
    ],
    [
        'name' => 'Sourabh Saini',
        'designation' => 'Customer Relations',
        'image' => 'assets/team-members/sourabh-saini.jpeg'
    ],
    [
        'name' => 'Md Imran',
        'designation' => 'Sales Director',
        'image' => 'assets/team-members/md-imran.jpeg'
    ],
    [
        'name' => 'Manish Kumar',
        'designation' => 'Product Designer',
        'image' => 'assets/team-members/manish.jpeg'
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
