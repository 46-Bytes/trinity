{{-- video-player.blade.php --}}
<div class="video-container">
    <div id="container-{{ $id }}" style="position: relative;">
        <video
                id="{{ $id ?? 'video-player' }}"
                class="video-js vjs-default-skin vjs-big-play-centered"
                controls
                preload="auto"
                data-setup='{"bigPlayButton": false}'
                poster="{{ $poster ?? '' }}"
        >
            <source src="{{ $path }}" type="{{ $type ?? 'video/mp4' }}"/>
            <p class="vjs-no-js">
                To view this video, please enable JavaScript, and consider upgrading to a
                web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>.
            </p>
        </video>

        <button
                id="play-{{ $id }}"
                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 999; background: none; border: none; cursor: pointer;"
                type="button"
        >
            <i class="fa-duotone fa-solid fa-circle-play" style="color: white; font-size: 50px;"></i>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const originalWidth = {{ $width ?? '640' }};
            const originalHeight = {{ $height ?? '360' }};
            const aspectRatio = originalHeight / originalWidth;
            const isAutoplay = {{ isset($autoplay) && $autoplay ? 'true' : 'false' }};

            // Initialize player first
            const player = videojs('{{ $id }}');
            const playButton = document.getElementById('play-{{ $id }}');

            function setVideoDimensions() {
                const container = document.getElementById('container-{{ $id }}');
                const screenWidth = window.innerWidth;
                let videoWidth, videoHeight;

                if (screenWidth >= originalWidth + 40) {
                    videoWidth = originalWidth;
                    videoHeight = originalHeight;
                } else {
                    const margin = screenWidth > 768 ? 40 : 20;
                    videoWidth = screenWidth - margin;
                    videoHeight = Math.floor(videoWidth * aspectRatio);
                }

                container.style.width = `${videoWidth}px`;
                container.style.height = `${videoHeight}px`;
                player.dimensions(videoWidth, videoHeight);
            }

            // Handle play button click
            playButton.addEventListener('click', () => {
                try {
                    player.play()
                        .then(() => {
                            playButton.style.display = 'none';
                        })
                        .catch((error) => {
                            console.error('Error playing video:', error);
                        });
                } catch (error) {
                    console.error('Error playing video:', error);
                }
            });

            // Hide play button when video starts playing
            player.on('play', () => {
                playButton.style.display = 'none';
            });

            // Show play button when video is paused
            player.on('pause', () => {
                playButton.style.display = 'block';
            });

            // Show play button when video ends
            player.on('ended', () => {
                playButton.style.display = 'block';
            });

            // Handle initial autoplay state
            if (isAutoplay) {
                playButton.style.display = 'none';
            }

            // Set initial dimensions
            setVideoDimensions();

            // Update dimensions on window resize
            window.addEventListener('resize', _.debounce(setVideoDimensions, 250));
        });
    </script>
</div>
