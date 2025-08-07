<?php

namespace App\View\Components;

use Illuminate\View\Component;

class VideoPlayer extends Component {
    public string $path;
    public string $type;
    public string $id;
    public int $width;
    public int $height;
    public bool $autoplay = false;
    public bool $muted = false;
    public ?string $poster;

    public function __construct(string $path, string $type = 'video/mp4', string $id = 'video-player', int $width = 640, int $height = 360, bool $autoplay = false, bool $muted = false, string $poster = null) {
        $this->path = $path;
        $this->type = $type;
        $this->id = $id;
        $this->width = $width;
        $this->height = $height;
        $this->autoplay = $autoplay;
        $this->muted = $muted;
        $this->poster = $poster;
    }

    public function render() {
        return view('components.video-player');
    }
}
