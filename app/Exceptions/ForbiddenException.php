<?php

namespace App\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // not applicable...
    }


    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return $request->expectsJson()
            ? response()->json(['message' => 'Forbidden'], 403)
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
