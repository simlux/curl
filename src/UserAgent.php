<?php declare(strict_types=1);

namespace Simlux\Curl;

/**
 * Class UserAgent
 *
 * @package Simlux\Curl
 */
class UserAgent
{
    const BROWSER_FIREFOX = 'Mozilla/5.0';

    /**
     * @see https://msdn.microsoft.com/en-us/library/ms537503(v=vs.85).aspx#VerToken
     */
    const OS_WINDOWS_81          = 'Windows NT 6.3';
    const OS_WINDOWS_8           = 'Windows NT 6.2';
    const OS_WINDOWS_7           = 'Windows NT 6.1';
    const OS_WINDOWS_VISTA       = 'Windows NT 6.0';
    const OS_WINDOWS_SERVER_2003 = 'Windows NT 5.2';
    const OS_WINDOWS_XP_X64      = 'Windows NT 5.2';
    const OS_WINDOWS_XP          = 'Windows NT 5.1';
    const OS_WINDOWS_2000_SP1    = 'Windows NT 5.01';
    const OS_WINDOWS_2000        = 'Windows NT 5.0';
    const OS_WINDOWS_NT_40       = 'Windows NT 4.0';
    const OS_WINDOWS_MILLENNIUM  = 'Windows 98; Win 9x 4.90';
    const OS_WINDOWS_98          = 'Windows 98';
    const OS_WINDOWS_95          = 'Windows 95';
    const OS_WINDOWS_CE          = 'Windows CE';


    public $browser;
    public $browserVersion;
    public $os;
    public $osVersion;
}