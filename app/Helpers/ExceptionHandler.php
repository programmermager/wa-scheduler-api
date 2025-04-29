<?php

namespace App\Helpers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ExceptionHandler
{
    public static function handle(callable $callback)
    {
        try {
            return $callback();
        } catch (QueryException $e) {
            return self::handleSqlException($e);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected static function handleSqlException(QueryException $e)
    {
        $errorCode = $e->errorInfo[1];

        $message = match ($errorCode) {
            1062 => 'Data sudah ada (duplikat).',
            1451 => 'Data tidak bisa dihapus karena masih digunakan di data lain.',
            1048 => 'Ada kolom yang wajib diisi namun kosong.',
            default => 'Terjadi kesalahan saat memproses data.',
        };

        return response()->json([
            'status' => false,
            'message' => $message,
            'debug' => config('app.debug') ? $e->getMessage() : null,
        ], 422);
    }
}
