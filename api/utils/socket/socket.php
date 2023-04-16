<?php

/**
 * Send a wakeup message to the socket edge server to
 * refresh the client's message page.
 *
 * @param string $handle The handle of the client to wake up.
 * @return void
 */
function sendWakeupMessage(string $handle)
{
    global $SOCKET_HOST, $SOCKET_THROW;

    try {
        $url = "http://$SOCKET_HOST:5556/message/$handle";
        $res = file_get_contents($url);
    } catch (Exception $e) {
        if ($SOCKET_THROW) {
            throw $e;
        }
    }
}
