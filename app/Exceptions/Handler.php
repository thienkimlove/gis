<?php
namespace Gis\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Gis\Models\SystemCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * The Exception handling class
 * To handle over all exception occured in the application
 */
class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = array(
        'Symfony\Component\HttpKernel\Exception\HttpException'
    );

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e            
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Gis Handler Exception.
     * That handles all kind of exceptions thrown by application
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Exception $e:
     *            the exception will be handled
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $e)
    {
        $code = SystemCode::UNHANDLED_ERROR;
        $message = $e->getMessage();
        if ($e instanceof GisException) {
            return response()->json(buildResponseMessage($message, $e->getCode()));
        }elseif ($e instanceof ModelNotFoundException) {
            $code = SystemCode::NOT_FOUND;
            $message = trans('common.common_data_not_found');
        }
        elseif ($e instanceof NotFoundHttpException) {
            $code = SystemCode::NOT_FOUND;
            $message = trans('common.common_data_not_found');
        } elseif ($e instanceof \PDOException) {
            $code = SystemCode::DB_ERROR;
            $message = trans('common.database_error');
        }
        
        if ($request->ajax()) {
            return response()->json(buildResponseMessage($message, $code));
        } else {
            if (view()->exists('errors.' . $code))
                return redirect('server-error/' . $code);
            else
                return redirect('server-error/' . SystemCode::UNHANDLED_ERROR);
        }
    }
}
