<?php

defined('C5_EXECUTE') or die('Access Denied.');

class RemoPhasherPackage extends Package {

    protected $pkgHandle = 'remo_phasher';
    protected $appVersionRequired = '5.6.2.1';
    protected $pkgVersion = '0.9';

    public function getPackageDescription() {
        return t("Adds a perceptual hash to images.");
    }

    public function getPackageName() {
        return t("Image pHasher");
    }

    public function install() {
        $pkg = parent::install();

        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    }

    public function upgrade() {
        $pkg = Package::getByHandle($this->pkgHandle);
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');

        parent::upgrade();
    }

    protected function registerPackageClasses() {
        $classes = array(
            'PHasher' => array('library', '3rdparty/phasher.class', $this->pkgHandle)
        );
        Loader::registerAutoload($classes);
    }

    protected function registerEvents() {
        Events::extend('on_file_add', function ($f, $fv) {
                if ($fv->getTypeObject() == FileType::T_IMAGE) {
                    $filePath = $fv->getPath();

                    $phasherInstance = PHasher::Instance();

                    $hash = $phasherInstance->HashImage($filePath, REMO_PHASER_ROTATION, REMO_PHASER_MIRRORED, REMO_PHASER_SIZE);
                    $hashAsString = $phasherInstance->HashAsString($hash, REMO_PHASER_HEX_STRING);

                    $fv->setAttribute('image_hash', $hashAsString);
                }
            });
    }

    protected function defineConstants() {
        if (!defined('REMO_PHASER_ROTATION')) {
            define('REMO_PHASER_ROTATION', 0);
        }
        if (!defined('REMO_PHASER_MIRRORED')) {
            define('REMO_PHASER_MIRRORED', 0);
        }
        if (!defined('REMO_PHASER_SIZE')) {
            define('REMO_PHASER_SIZE', 8);
        }
        if (!defined('REMO_PHASER_CELLSIZE')) {
            define('REMO_PHASER_CELLSIZE', 10);
        }
        if (!defined('REMO_PHASER_HEX_STRING')) {
            define('REMO_PHASER_HEX_STRING', false);
        }
    }

    public function on_start() {
        $this->registerPackageClasses();
        $this->registerEvents();
        $this->defineConstants();
    }

}