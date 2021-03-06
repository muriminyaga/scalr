<?php
class Scalr_UI_Controller_Admin_Utils extends Scalr_UI_Controller
{
    public function hasAccess()
    {
        return $this->user && ($this->user->getType() == Scalr_Account_User::TYPE_SCALR_ADMIN);
    }

    public function getPermissions($path)
    {
        $result = array();
        foreach(scandir($path) as $p) {
            if ($p == '.' || $p == '..' || $p == '.svn')
                continue;

            $p1 = $path . '/' . $p;

            if (is_dir($p1)) {
                $result = array_merge($result, $this->getPermissions($p1));
                continue;
            }

            $p1 = str_replace(SRCPATH . '/', '', $p1);
            $p1 = str_replace('.php', '', $p1);
            $p1 = str_replace('/', '_', $p1);

            $result[str_replace('Scalr_UI_Controller_', '', $p1)] = 'Not covered';
        }

        return $result;
    }

    public function mapPermissionsAction()
    {
        $this->response->page('ui/admin/utils/mapPermissions.js', array('map' => $this->getPermissions(SRCPATH . '/Scalr/UI/Controller')));
    }

    public function debugAction()
    {
        $this->response->page('ui/admin/utils/debug.js', Scalr_Session::getInstance()->getDebugMode());
    }

    public function xSaveDebugAction()
    {
        Scalr_Session::getInstance()->setDebugMode(array(
            'sql' => $this->getParam('sql')
        ));

        $js = array();

        if ($this->getParam('sql')) {
            $js[] = $this->response->getModuleName('ui-debug.js');
        }

        if (count($js))
            $this->response->data(array('js' => $js));

        $this->response->success('Debug parameters have applied');
    }
}
