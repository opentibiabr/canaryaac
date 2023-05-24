<?php
/**
 * Client Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Controller\Admin\SweetAlert;
use App\Model\Functions\ClientEditor;
use App\Utils\View;

class Client extends Base
{

    public static function editClient($request)
    {
        $postVars = $request->getPostVars();
        $postFiles = $request->getPostFiles();

        $generatedExePath = __DIR__ . './../../../resources/generated-exe/';

        $exeEditor = new ClientEditor();

        if (empty($postVars['client_rsa'])) {
            $status = SweetAlert::Types('Error!', 'You did not pick client.exe file or it was too big to upload', 'error', 'btn btn-danger');
            return self::viewCreateClient($request, $status);
        }

        $exeEditor->setRsa($postVars['client_rsa']);
        foreach ($exeEditor->getServices() as $key => $value) {
            if (isset($postVars[$key])) {
                $exeEditor->setService($key, (string)$postVars[$key]);
            }
        }
        if (empty($postFiles['client_exe']['tmp_name'])) {
            $status = SweetAlert::Types('Error!', 'You did not pick client.exe file or it was too big to upload', 'error', 'btn btn-danger');
            return self::viewCreateClient($request, $status);
        }
        if (!is_uploaded_file($postFiles['client_exe']['tmp_name'])) {
            $status = SweetAlert::Types('Error!', 'You did not pick client.exe file or it was too big to upload', 'error', 'btn btn-danger');
            return self::viewCreateClient($request, $status);
        }

        $exeFilePath = $postFiles['client_exe']['tmp_name'];
        $file_name = 'client_' . microtime(true) . '.zip';
        $client_url = URL . '/resources/generated-exe/' . $file_name;
        $newExeZipFilePath = $generatedExePath . $file_name;
        $exeEditor->progress($exeFilePath, $newExeZipFilePath);

        $sweetAlert = SweetAlert::Types('Success!', '', 'success', 'btn btn-success');
        return self::viewCreateClient($request, $sweetAlert, $client_url);
    }

    public static function viewCreateClient($request, $status = null, $client_url = null)
    {
        $content = View::render('admin/modules/client/index', [
            'sweetAlert' => $status,
            'client_url' => $client_url
        ]);
        return parent::getPanel('Create Client', $content, 'client');
    }
}