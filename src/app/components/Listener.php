<?php

use Phalcon\Acl\Adapter\Memory;

class Listener
{
    function check()
    {
        echo "event triggered";
    }
    function beforeHandleRequest()
    {
        // echo "before event";
        // die;
        $aclFile = APP_PATH . '/security/acl.cache';
        // Check whether ACL data already exist
        if (true !== is_file($aclFile)) {

            // The ACL does not exist - build it
            $acl = new Memory();

            // ... Define roles, components, access, etc
            $acl->addRole('manager');
            $acl->addComponent('index', ['index']);
            $acl->allow('manager', 'index', 'index');

            // Store serialized list into plain file
            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        } else {
            // Restore ACL object from serialized file
            $acl = unserialize(
                file_get_contents($aclFile)
            );
        }

        // Use ACL list as needed
        if (true === $acl->isAllowed('manager', 'index', 'index')) {
            echo 'Access granted!';
            // die;
        } else {
            echo 'Access denied :(';
            die;
        }
    }
}
