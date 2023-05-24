<?php
/**
 * Upload Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\Uploads;
use App\Model\Functions\Upload as FunctionsUpload;
use App\Utils\View;

class Upload extends Base
{

    public static function upload($request)
    {
        $postVars = $request->getPostFiles();

        /**
         * FORM ONE FILE
         */
        /*
        if (isset($postVars['file'])) {
            $upload = new FunctionsUpload($postVars['file']);
            $upload->generateNewName();
            $success = $upload->upload(__DIR__.'/../../../resources/upload', false);
            if ($success) {
                $status = SweetAlert::Types('Success!', $upload->getBaseName(), 'success', 'btn btn-success');;
                return self::viewUpload($request, $status);
            }
        }
        */

        /**
         * FORM MULTIPLE FILES
         */
        if (empty($postVars['file']['name'][0])) {
            $status = SweetAlert::Types('Error!', 'You need to select some file.', 'error', 'btn btn-danger');
            return self::viewUpload($request, $status);
        }
        $message = '';
        if (isset($postVars['file']['name'])) {
            $upload = FunctionsUpload::createMultiUpload($postVars['file']);
            foreach ($upload as $key => $value) {
                //$value->generateNewName();
                $value->setName('upload-admin');
                if ($value->getExtension() != 'png') {
                    $status = SweetAlert::Types('Error!', 'Invalid file extension.', 'error', 'btn btn-danger');
                    return self::viewUpload($request, $status);
                    exit;
                }
                $success = $value->upload('upload', false);
                if ($success) {
                    $message .= 'Uploaded ' . $value->getBaseName() . '';
                    continue;
                }
            }
            $status = SweetAlert::Types('Success!', $message, 'success', 'btn btn-success');
            return self::viewUpload($request, $status);
            exit;
        }
        $status = SweetAlert::Types('Error!', 'You need to select some file.', 'error', 'btn btn-danger');
        return self::viewUpload($request, $status);
    }

    public static function getUploads()
    {
        $select_uploads = Uploads::getUploads(null, 'id DESC');
        while ($upload = $select_uploads->fetchObject()) {
            $arrayUploads[] = [
                'id' => $upload->id,
                'name' => $upload->name,
                'path' => $upload->path,
                'date' => date('M d Y H:i:s', $upload->date)
            ];
        }
        return $arrayUploads ?? [];
    }

    public static function viewUpload($request, $status = null)
    {
        $content = View::render('admin/modules/upload/index', [
            'sweetAlert' => $status,
            'uploads' => self::getUploads()
        ]);
        return parent::getPanel('Upload', $content, 'upload');
    }
}