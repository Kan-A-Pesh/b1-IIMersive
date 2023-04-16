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
    // ! This works for Docker only, consider using
    // ! localhost or 127.0.0.1 if not using Docker.

    $url = 'http://host.docker.internal:5556/message/' . $handle;
    $res = file_get_contents($url);
}
