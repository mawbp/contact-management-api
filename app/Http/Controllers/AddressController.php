<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressUpdateRequest;
use App\Http\Requests\AdressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ContactResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact): Contact
    {
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress): Address
    {
        $address = Address::query()->where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function create(int $idContact, AdressCreateRequest $req): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

        $data = $req->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $req): AddressResource
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $data = $req->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function list(int $idContact): JsonResponse
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $addresses = Address::query()->where('contact_id', $contact->id)->get();

        return (AddressResource::collection($addresses))->response()->setStatusCode(200);

    }
}
