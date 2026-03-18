<?php

use Livewire\Component;

new class extends Component
{
    public $file;

    public function isImage(): bool
    {
        return preg_match('/^image\/\w+$/', $this->file->mime);
    }

    public function isPdf(): bool
    {
        
        $pdfMimeTypes = [
            'application/pdf',
            'application/x-pdf',
            'application/acrobat',
            'application/vnd.pdf',
            'text/pdf',
            'text/x-pdf',
        ];

        return in_array(
            $this->file->mime,
            $pdfMimeTypes,
            true // strict comparison
        );
    }

    public function isWord(): bool
    {
        
        $wordMimeTypes = [
             'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.ms-word.template.macroEnabled.12',
        ];

        return in_array(
            $this->file->mime,
            $wordMimeTypes,
            true // strict comparison
        );
    }

    public function isAudio(): bool
    {
        
        $audioMimeTypes = [
            'audio/mpeg',
            'audio/ogg',
            'audio/wav',
            'audio/x-m4a',
            'audio/webm',
        ];

        return in_array(
            $this->file->mime,
            $audioMimeTypes,
            true // strict comparison
        );
    }

    public function isVideo(): bool
    {
        
        $videoMimeTypes = [
            'video/mp4',
            'video/mpeg',
            'video/ogg',
            'video/quicktime',
            'video/webm',
        ];

        return in_array(
            $this->file->mime,
            $videoMimeTypes,
            true // strict comparison
        );
    }

    public function isExcel(): bool
    {
        
        $excelMimeTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
        ];

        return in_array(
            $this->file->mime,
            $excelMimeTypes,
            true // strict comparison
        );
    }

    public function isPowerPoint(): bool
    {
        
        $powerPointMimeTypes = [
            'application/vnd.ms-powerpoint',                              // legacy .ppt, .pot, .pps, .ppa (PowerPoint 97-2003)
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',  // modern .pptx (default 2007+)
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12', // macro-enabled .pptm
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',     // .ppsx (slideshow mode)
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',    // macro-enabled slideshow .ppsm
            'application/vnd.openxmlformats-officedocument.presentationml.template',      // .potx (template)
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
        ];

        return in_array(
            $this->file->mime,
            $powerPointMimeTypes,
            true // strict comparison
        );
    }

    public function isZip(): bool
    {
        
        $zipMimeTypes = [
            'application/zip',
        ];

        return in_array(
            $this->file->mime,
            $zipMimeTypes,
            true // strict comparison
        );
    }

    public function isText(): bool
    {
        
        $textMimeTypes = [
            'text/plain',
            'text/html',
            'text/css',
            'text/javascript',
            'text/csv',
        ];

        return in_array(
            $this->file->mime,
            $textMimeTypes,
            true // strict comparison
        );
    }
};
?>

<div>
    <span class="w-5 h-5 inline-flex items-center justify-center mr-2">
        @if ($this->file->is_folder)
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
            </svg>
        @elseif ($this->isPdf())
           <img class="max-w-full" :src="`/images/icon/pdf.png`">
        @elseif ($this->isWord())
           <img class="max-w-full" :src="`/images/icon/word.png`">
        @elseif ($this->isExcel())
           <img class="max-w-full" :src="`/images/icon/excel.png`">
        @elseif ($this->isImage())
           <img class="max-w-full" :src="`/images/icon/image.png`">
        @elseif ($this->isZip())
           <img class="max-w-full" :src="`/images/icon/zip.png`">
        @elseif ($this->isText())
           <img class="max-w-full" :src="`/images/icon/txt-file.png`">
        @elseif ($this->isAudio())
           <img class="max-w-full" :src="`/images/icon/audio.png`">
        @elseif ($this->isVideo())
           <img class="max-w-full" :src="`/images/icon/video.png`">
        @elseif ($this->isPowerPoint())
           <img class="max-w-full" :src="`/images/icon/powerpoint.png`">
        @else
           <img class="max-w-full" :src="`/images/icon/attach-file.png`">
        @endif
    </span>
</div>