<?php

namespace Guangzhong\Xhgui;

use Guangzhong\Xhgui\Saver\File;
use Guangzhong\Xhgui\Saver\Mongo;
use Guangzhong\Xhgui\Saver\Upload;
use MongoDB\Client;
use MongoDB\Collection;

/**
 * A small factory to handle creation of the profile saver instance.
 *
 * This class only exists to handle cases where an incompatible version of pimple
 * exists in the host application.
 */
class Saver
{
    /**
     * Get a saver instance based on configuration data.
     *
     * @param array $config The configuration data.
     *
     * @return Xhgui_Saver_File|Xhgui_Saver_Mongo|Xhgui_Saver_Upload
     */
    public static function factory($config)
    {
        switch ($config['save.handler']) {

            case 'file':
                return new File($config['save.handler.filename']);

            case 'upload':
                $timeout = 3;
                if (isset($config['save.handler.upload.timeout'])) {
                    $timeout = $config['save.handler.upload.timeout'];
                }

                return new Upload(
                    $config['save.handler.upload.uri'],
                    $timeout
                );

            case 'mongodb':
            default:
                $mongo = new Client($config['db.host'], $config['db.options']);
                $collection = new Collection($mongo->getManager(), $config['db.db'], 'results');

                return new Mongo($collection);
        }
    }
}
