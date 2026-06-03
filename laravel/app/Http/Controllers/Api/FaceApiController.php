<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaceRequest;
use App\Models\Face;
use App\Models\Student;
use App\Services\FlaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Exception;

class FaceApiController extends Controller
{
    public function __construct(private FlaskService $flaskService) {}

    /**
     * POST /api/faces/register
     * Kirim foto ke Flask, simpan embedding hasilnya
     */
    public function register(StoreFaceRequest $request): JsonResponse
    {
        try {
            $student = Student::findOrFail($request->student_id);

            // Kirim ke Flask untuk extract embedding
            $result = $this->flaskService->registerFace(
                $student->id,
                $request->image
            );

            if (! ($result['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal memproses wajah.',
                    'code'    => $result['code'] ?? 'FACE_PROCESSING_FAILED',
                ], 422);
            }

            // Simpan foto ke storage
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $request->image);
            $filename  = 'faces/' . $student->nim . '_' . time() . '.jpg';
            Storage::disk('public')->put($filename, base64_decode($imageData));

            // Nonaktifkan embedding lama
            Face::where('student_id', $student->id)->update(['is_active' => false]);

            // Simpan face baru dengan embedding dari Flask
            $face = Face::create([
                'student_id'    => $student->id,
                'photo_path'    => $filename,
                'embedding'     => json_encode($result['embedding']),
                'model_version' => $result['model'] ?? 'DeepFace-ArcFace',
                'is_active'     => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Wajah {$student->name} berhasil didaftarkan.",
                'data'    => [
                    'face_id'   => $face->id,
                    'student'   => $student->name,
                    'model'     => $face->model_version,
                    'photo_url' => asset('storage/' . $filename),
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan.',
                'code'    => 'STUDENT_NOT_FOUND',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code'    => 'INTERNAL_ERROR',
            ], 500);
        }
    }

    /**
     * DELETE /api/faces/{face}
     */
    public function destroy(Face $face): JsonResponse
    {
        $face->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Data wajah berhasil dinonaktifkan.',
        ]);
    }
}
