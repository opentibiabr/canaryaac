<?php
/**
 * SweetAlert Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Utils\View;

class SweetAlert
{
    public static function Types($title = 'Success!', $message = null, $icon = 'success', $confirmButton = 'btn btn-primary')
    {
        return View::render('admin/sweetalert/types', [
            'title' => $title,
            'text' => $message,
            'icon' => $icon,
            'confirmButton' => $confirmButton
        ]);
    }

}
