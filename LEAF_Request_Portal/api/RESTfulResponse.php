<?php
/*
 * As a work of the United States government, this project is in the public domain within the United States.
 */

abstract class RESTfulResponse
{
    /**
     * Returns result for HTTP GET requests
     * @param array $actionList
     * @return mixed
     */
    public function get($actionList)
    {
        return 'Method not implemented';
    }

    /**
     * Returns result for HTTP POST requests
     * @param array $actionList
     * @return mixed
     */
    public function post($actionList)
    {
        return 'Method not implemented';
    }

    /**
     * Returns result for HTTP DELETE requests
     * @param array $actionList
     * @return mixed
     */
    public function delete($actionList)
    {
        return 'Method not implemented';
    }

    /**
     * Handles HTTP request
     * @param string $action
     */
    public function handler($action)
    {
        $action = $this->parseAction($action);
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->output($this->get($action));

                break;
            case 'POST':
                if ($_POST['CSRFToken'] == $_SESSION['CSRFToken'])
                {
                    $this->output($this->post($action));
                }
                else
                {
                    $this->output('Invalid Token.');
                }

                break;
            case 'DELETE':
                if ($_GET['CSRFToken'] == $_SESSION['CSRFToken'])
                {
                    $this->output($this->delete($action));
                }
                else
                {
                    $this->output('Invalid Token.');
                }

                break;
            default:
                $this->output('unhandled method');

                break;
        }
    }

    /**
     * Outputs in specified format based on $_GET['format']
     * Default to JSON
     * @param string $out
     */
    public function output($out = '')
    {
        //header('Access-Control-Allow-Origin: *');
        $format = isset($_GET['format']) ? $_GET['format'] : '';
        switch ($format) {
            case 'json':
            default:
                header('Content-type: application/json');
                $jsonOut = json_encode($out);

                if ($_SERVER['REQUEST_METHOD'] === 'GET')
                {
                    $etag = md5($jsonOut);
                    header_remove('Pragma');
                    header_remove('Cache-Control');
                    header_remove('Expires');
                    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
                           && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag)
                    {
                        header("ETag: {$etag}", true, 304);
                        header('Cache-Control: must-revalidate, private');
                        exit;
                    }

                    header("ETag: {$etag}");
                    header('Cache-Control: must-revalidate, private');
                }

                echo $jsonOut;

                break;
            case 'php':
                echo serialize($out);

                break;
            case 'string':
                echo $out;

                break;
            case 'json-js-assoc':
                header('Content-type: application/json');
                $out2 = array();
                foreach ($out as $item)
                {
                    $out2[] = $item;
                }
                echo json_encode($out2);

                break;
            case 'jsonp':
                $callBackName = '';
                if (isset($_GET['callback']))
                {
                    $callBackName = htmlentities($_GET['callback']);
                }
                else
                {
                    if (isset($_GET['jsonpCallback']))
                    {
                        $callBackName = htmlentities($_GET['jsonpCallback']);
                    }
                    else
                    {
                        $callBackName = 'jsonpCallback';
                    }
                }
                echo "{$callBackName}(" . json_encode($out) . ')';

                break;
            case 'xml':
                header('Content-type: text/xml');
                $xml = new SimpleXMLElement('<?xml version="1.0"?><output></output>');
                $this->buildXML($out, $xml);
                echo $xml->asXML();

                break;
            case 'csv':
                //if $out is not an array, create one with the appropriate structure, preserving the original value of $out
                if (!is_array($out))
                {
                    $out = array(
                                'column' => array('error'),
                                'row' => array('error' => $out),
                            );
                }

                $columns = $this->flattenStructure($out);

                $items = array_keys($out);
                $columns = array_keys($out[$items[0]]);

                header('Content-type: text/csv');
                header('Content-Disposition: attachment; filename="Exported_' . time() . '.csv"');
                $header = '';
                foreach ($columns as $column)
                {
                    $header .= '"' . $column . '",';
                }
                $header = trim($header, ',');
                $buffer = "{$header}\r\n";
                foreach ($out as $line)
                {
                    foreach ($columns as $column)
                    {
                        if (is_array($line[$column]))
                        {
                            $buffer .= '"';
                            foreach ($line[$column] as $tItem)
                            {
                                $buffer .= $tItem . ':;';
                            }
                            $buffer = trim($buffer, ':;');
                            $buffer .= '",';
                        }
                        else
                        {
                            $temp = strip_tags($line[$column]);
                            $temp = str_replace('"', '""', $temp);
                            $buffer .= '"' . $temp . '",';
                        }
                    }
                    $buffer .= "\r\n";
                }
                echo $buffer;

                break;
            case 'htmltable':
                $columns = $this->flattenStructure($out);

                $body = '<table>';
                $body .= '<thead><tr>';
                foreach ($columns as $column)
                {
                    $body .= '<th>' . $column . '</th>';
                }
                $body .= '</tr></thead>';
                $body .= '<tbody>';
                foreach ($out as $line)
                {
                    $body .= '<tr>';
                    foreach ($columns as $column)
                    {
                        if (isset($line[$column]) && is_array($line[$column]))
                        {
                            $body .= '<td>';
                            foreach ($line[$column] as $tItem)
                            {
                                $body .= $tItem . ':;';
                            }
                            $body = trim($body, ':;');
                            $body .= '</td>';
                        }
                        else
                        {
                            $temp = isset($line[$column]) ? strip_tags($line[$column]) : '';
                            $body .= '<td>' . $temp . '</td>';
                        }
                    }
                    $body .= '</tr>';
                }
                $body .= '</tbody>';
                echo $body;

                break;
            case 'x-visualstudio': // experimental mode for visual studio
                header('Content-type: application/json');
                $out2 = [];
                foreach($out as $item) {
                    $out2['r' . $item['recordID']] = $item;
                }

                $jsonOut = json_encode($out2);

                if ($_SERVER['REQUEST_METHOD'] === 'GET')
                {
                    $etag = md5($jsonOut);
                    header_remove('Pragma');
                    header_remove('Cache-Control');
                    header_remove('Expires');
                    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
                        && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag)
                    {
                        header("ETag: {$etag}", true, 304);
                        header('Cache-Control: must-revalidate, private');
                        exit;
                    }

                    header("ETag: {$etag}");
                    header('Cache-Control: must-revalidate, private');
                }

                echo $jsonOut;
                break;
            case 'debug':
                echo '<pre>' . print_r($out, true) . '</pre>';

                break;
        }
    }

    /**
     * Parses url input into generic format
     * @param string api path
     * @return string parsed path
     */
    public function parseAction($action)
    {
        $actionList = explode('/', $action, 10);

        $key = '';
        $args = array();
        foreach ($actionList as $type)
        {
            if (is_numeric($type))
            {
                $key .= '[digit]/';
                $args[] = $type;
            }
            else
            {
                if (substr($type, 0, 1) == '_')
                {
                    $key .= '[text]/';
                    $args[] = substr($type, 1);
                }
                else
                {
                    $key .= "{$type}/";
                }
            }
        }
        $key = rtrim($key, '/');

        $action = array();
        $action['key'] = $key;
        $action['args'] = $args;

        return $action;
    }

    /**
     * Get API Version
     * @return int API_VERSION
     */
    public function getVersion()
    {
        return $this->API_VERSION;
    }

    /**
     * Aborts script if the referrer directory doesn't match the admin directory
     */
    public function verifyAdminReferrer()
    {
        if (!isset($_SERVER['HTTP_REFERER']))
        {
            echo 'Error: Invalid request. Missing Referer.';
            exit();
        }

        $tIdx = strpos($_SERVER['HTTP_REFERER'], '://');
        $referer = substr($_SERVER['HTTP_REFERER'], $tIdx);

        $url = '://' . HTTP_HOST;

        $script = $_SERVER['SCRIPT_NAME'];
        $apiOffset = strpos($script, '/api/');
        $script = substr($script, 0, $apiOffset + 1);

        $checkMe = strtolower($url . $script . 'admin');

        if (strncmp(strtolower($referer), $checkMe, strlen($checkMe)) !== 0)
        {
            echo 'Error: Invalid request. Mismatched Referer';
            exit();
        }
    }

    /**
     * Helper function to build an XML file
     */
    private function buildXML($out, $xml)
    {
        if (is_array($out))
        {
            $keys = array_keys($out);
            foreach ($keys as $key)
            {
                $tkey = is_numeric($key) ? "id_{$key}" : $key;
                if (is_array($out[$key]))
                {
                    $subXML = $xml->addChild($tkey);
                    $this->buildXML($out[$key], $subXML);
                }
                else
                {
                    $xml->addChild($tkey, $out[$key]);
                }
            }
        }
        else
        {
            $xml->addChild('text', $out);
        }
    }

    private function flattenStructureActionHistory(&$out, $key)
    {
        if(!isset($_GET['table']) && $_GET['table'] != 'action_history') {
            $out[$key]['action_history'] = '&table=action_history';
            return false;
        }

        foreach ($out[$key]['action_history'] as $akey => $aval) {
            $newKey = $key . '.' . $akey;
            $out[$newKey] = $out[$key];
            $out[$newKey]['actionHistory_id'] = $newKey;
            $out[$newKey]['actionHistory_userID'] = $aval['userID'];
            $out[$newKey]['actionHistory_time'] = $aval['time'];
            $out[$newKey]['actionHistory_actionTextPasttense'] = $aval['actionTextPasttense'];
            $out[$newKey]['actionHistory_approverName'] = $aval['approverName'];
            $out[$newKey]['actionHistory_comment'] = $aval['comment'];
        }
        unset($out[$key]);

        return ['recordID', 'actionHistory_id', 'actionHistory_userID', 'actionHistory_time',
            'actionHistory_actionTextPasttense', 'actionHistory_approverName', 'actionHistory_comment'];
    }

    /**
     * flattenStructureOrgchart performs an in-place restructure of orgchart data
     * within $out to fit 2D data structures
     * @param array $out   Target data structure
     * @param int   $index Current index
     * @param array $keys  Array keys within data.s1 object
     */
    private function flattenStructureOrgchart(&$out, $index, $keys)
    {
        // flatten out orgchart_employee fields
        // delete orgchart_position extended content
        foreach($keys as $id) {
            if(strpos($id, '_orgchart') !== false) {
                if(!isset($out[$index][$id]['positionID'])) {
                    $out[$index][$id . '_email'] = $out[$index][$id]['email'];
                    $out[$index][$id . '_userName'] = $out[$index][$id]['userName'];
                }
                unset($out[$index][$id]);
            }
        }
    }

    /**
     * flattenStructure performs an in-place restructure of $out to fit 2D data structures
     * @param array $out Target data structure
     * @return array Column headers
     */
    private function flattenStructure(&$out)
    {
        $table = isset($_GET['table']) ? $_GET['table'] : '';

        $columns = ['recordID', 'serviceID', 'date', 'userID', 'title', 'lastStatus', 'submitted',
            'deleted', 'service', 'abbreviatedService', 'groupID'];

        // flatten out s1 value, which is map of data fields -> values
        $hasActionCols = false;
        foreach ($out as $key => $item)
        {
            if (isset($item['s1']))
            {
                $out[$key] = array_merge($out[$key], $item['s1']);
                unset($out[$key]['s1']);

                $this->flattenStructureOrgchart($out, $key, array_keys($item['s1']));
            }

            if (isset($item['action_history']))
            {
                $actionCols = $this->flattenStructureActionHistory($out, $key);
                if($actionCols !== false) {
                    $hasActionCols = true;
                    $columns = $actionCols;
                }
            }

            foreach(array_keys($out[$key]) as $tkey) {
                if(!in_array($tkey, $columns)) {
                    $columns[] = $tkey;
                }
            }
        }

        return $columns;
    }
}
