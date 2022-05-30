<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends BaseController
{
    function create(StoreAddressRequest $request)
    {
        try {
            \DB::beginTransaction();
            $chekAddress = auth()->user()->address()->count();
            $address = new Address();
            $address->fill($request->all());
            if ($chekAddress == 0) {
                $address->is_favorite = 1;
                $user = auth()->user();
                $user->fill($request->only('name', 'address'));
                if ($user->email == '') {
                    $user->email = $request->email;
                }
                $user->save();
            }
            auth()->user()->address()->save($address);
            \DB::commit();
            return $this->sendSuccess('ADDRESS ADDED SUCCESSFULLY');
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function changeAddressStatus($id)
    {
        try {
            \DB::beginTransaction();
            $chekAddress = auth()->user()->address()->find($id);
            if (empty($chekAddress)) {
                return $this->sendFailed('ADDRESS NOT FOUND', 200);
            }
            auth()->user()->address()->update(['is_favorite' => 0]);
            $chekAddress->is_favorite = 1;
            auth()->user()->address()->save($chekAddress);
            \DB::commit();
            return $this->sendSuccess('STATUS UPDATE SUCCESSFULLY');
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function index()
    {
        $address = auth()->user()->address()->select('id', 'type', 'name', 'mobile', 'email', 'pincode', 'address', 'is_favorite', 'latitude', 'longitude', 'location')->latest()->get();
        if (!isset($address) || count($address) == 0) {
            return $this->sendFailed('ADDRESS NOT FOUND', 200);
        }
        return $this->sendSuccess('ADDRESS GET SUCCESSFULLY', ($address));
    }

    function update(StoreAddressRequest $request)
    {
        try {
            \DB::beginTransaction();
            $address = Address::find($request->id);
            if ($address == null)
                return $this->sendFailed('ADDRESS NOT FOUND', 200);
            $address->fill($request->all());
            auth()->user()->address()->save($address);
            \DB::commit();
            return $this->sendSuccess('ADDRESS UPDATED SUCCESSFULLY');
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function delete($id)
    {
        try {
            \DB::beginTransaction();
            $address = auth()->user()->address()->find($id);
            if (!isset($address) || $address == null) {
                return $this->sendFailed('ADDRESS NOT FOUND', 200);
            }
            $address->delete();
            \DB::commit();
            return $this->sendSuccess('ADDRESS DELETE SUCCESSFULLY');
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
}
