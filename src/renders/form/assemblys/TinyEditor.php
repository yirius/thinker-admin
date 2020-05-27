<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class TinyEditor
 * @package Yirius\Admin\renders\form\assemblys
 * @method TinyEditor setLanguage($language);
 * @method TinyEditor setPlugins($plugins);
 * @method TinyEditor setToolbar($toolbar);
 * @method TinyEditor setResize(bool $resize);
 * @method TinyEditor setElementpath(bool $elementpath);
 * @method TinyEditor setBranding(bool $branding);
 * @method TinyEditor setMenubar($menubar);
 * @method TinyEditor setHeight(int $height);
 * @method TinyEditor setImageUploadUrl(string $imageUploadUrl);
 * @method TinyEditor setQuickbarsSelectionToolbar(string $quickbarsSelectionToolbar);
 * @method TinyEditor setContextmenuNeverUseNative(bool $contextmenuNeverUseNative);
 */
class TinyEditor extends Textarea
{
    protected $attrsFields = [
        "language","plugins","toolbar","resize","elementpath","branding","menubar","height",
        "imageUploadUrl", "quickbarsSelectionToolbar", "contextmenuNeverUseNative"
    ];

    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        ThinkerAdmin::script("tinymce", false, true);

        $this->addAttr("lay-tinymce", "");
    }
}