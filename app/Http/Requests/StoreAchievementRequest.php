<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;


class StoreAchievementRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  public function calculateScore($description, $title, $institution, $level, $ranking): float
    {
        $queryString = http_build_query([
            'deskripsi' => $description,
            'judul' => $title,
            'instansi' => $institution,
            'tingkat' => $level,
            'peringkat' => $ranking,
        ]);
        $apiEndpoint = config('app.api_endpoint') . '?' . $queryString;

        try {
            $response = Http::post($apiEndpoint);
            $data = $response->json();

            if (isset($data['result'])) {
                return (float) $data['result'];
            } else {
                // Tangani jika respon tidak memiliki hasil yang diharapkan
                throw new \Exception("API response doesn't contain the expected 'result' field.");
            }
        } catch (\Exception $e) {
            // Tangani pengecualian yang terjadi selama permintaan HTTP
            // Anda dapat mengembalikan nilai default atau melakukan penanganan sesuai kebijakan Anda
            return 0.0; // Misalnya, mengembalikan nilai 0 sebagai nilai default jika terjadi kesalahan
        }
    }


  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, Rule|array|string>
   */
  public function rules(): array
  {

    return [
      'name' => ['required', 'max:255'],
      'description' => ['required', 'max:1024'],
      'level_id' => ['required', 'integer', 'exists:levels,id'],
      'rank_id' => ['required', 'integer', 'exists:ranks,id'],
      'user_id' => ['required', 'integer', 'exists:users,id'],
      'location' => ['required', 'string', 'max:255'],
      'organizer' => ['required', 'string', 'max:255'],
      'start_date' => ['required', 'date'],
      'end_date' => ['required', 'date', 'after_or_equal:start_date'],
      'idea_file' => ['file', 'max:4096', 'mimes:jpg,png,jpeg,pdf', 'required'],
      'poster_file' => ['file', 'max:4096', 'mimes:png,jpg,jpeg,pdf', 'required'],
      'documentation_file' => ['file', 'max:6144', 'mimes:png,jpeg,jpg,pdf', 'required'],
      'certificate_file' => ['file', 'max:4096', 'mimes:png,jpg,jpeg,pdf', 'required'],
      'score' => ['nullable', 'numeric'],
    ];
  }

  public function messages(): array
  {
    return [
      'documentation_file.max' => 'Ukuran file Ide/Karya tidak boleh lebih dari 2MB.',
      'poster_file.max' => 'Ukuran file Poster/Undangan Kompetisi tidak boleh lebih dari 2MB.',
      'photo_file.max' => 'Ukuran file Foto/Dokumentasi tidak boleh lebih dari 2MB.',
      'certificate_file.max' => 'Ukuran file Sertifikat tidak boleh lebih dari 2MB.',
      'documentation_file.mimes' => 'File Ide/Karya harus berformat PDF.',
      'poster_file.mimes' => 'File Poster/Undangan Kompetisi harus berformat PNG atau JPG.',
      'photo_file.mimes' => 'File Foto/Dokumentasi harus berformat PNG atau JPG.',
      'certificate_file.mimes' => 'File Sertifikat harus berformat PDF.',
    ];
  }

}
