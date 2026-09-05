<?php

namespace App\Http\Requests\Publication;

use App\Models\Publication;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Only the publication's owner may change its reservation availability.
     * Route model binding resolves {publication} before this runs, so it's
     * already available here - this is the server-side ownership check the
     * frontend's own restrictions must never be trusted to replace.
     */
    public function authorize(): bool
    {
        $publication = $this->route('publication');

        return $publication !== null && $this->user()?->can('update', $publication);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reservation_enabled' => ['required', 'boolean'],
        ];
    }

    /**
     * Reservation availability only makes sense for Driver posts - reject
     * the (authorized, well-formed) request rather than silently letting a
     * Passenger post carry a meaningless reservation state.
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $publication = $this->route('publication');

            if ($publication && ! $publication->isDriverPost()) {
                $validator->errors()->add(
                    'reservation_enabled',
                    'Reservation availability only applies to Driver posts.'
                );
            }
        });
    }
}
