<?php

namespace App\Repository\Api\Driver;

use DB;
use Carbon\Carbon;
use App\Models\Trip;
use App\Models\User;
use App\Models\Setting;
use Carbon\CarbonPeriod;
use App\Traits\PhotoTrait;
use App\Models\DriverWallet;
use App\Models\Notification;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Models\DriverDetails;
use App\Models\DriverDocuments;
use App\Repository\ResponseApi;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TripResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DriverResource;
use App\Http\Resources\WalletResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DriverDocumentResource;
use App\Interfaces\Api\Driver\DriverRepositoryInterface;
use App\Models\Slider;
use App\Traits\GeneralTrait;
use App\Traits\FirebaseNotification;
use Illuminate\Support\Facades\DB as FacadesDB;

class DriverRepository extends ResponseApi implements DriverRepositoryInterface
{
    use PhotoTrait, FirebaseNotification;

    public function registerDriver(Request $request): JsonResponse
    {
        try {

            $rules = [
                'bike_type' => 'required',
                'bike_model' => 'required',
                'bike_color' => 'required',
                'area_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $user_id = Auth::user()->id;
            $storeNewDriverDetails = DriverDetails::query()->updateOrCreate([
                'driver_id' => $user_id
            ], [
                'bike_type' => $request->bike_type,
                'bike_model' => $request->bike_model,
                'bike_color' => $request->bike_color,
                'area_id' => $request->area_id,
                'driver_id' => $user_id
            ]);

            if (isset($storeNewDriverDetails)) {
                return self::returnResponseDataApi(new DriverResource($storeNewDriverDetails), "تم تسجيل بيانات السائق بنجاح", 200);
            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // registerDriver

    public function registerDriverDoc(Request $request): JsonResponse
    {
        try {

            $rules = [
                'agency_number' => 'required|image',
                'bike_license' => 'required|image',
                'id_card' => 'required|image',
                'house_card' => 'required|image',
                'bike_image' => 'required|image',
            ];

            $msg = [
                'agency_number.image' => 'agency number must be image',
                'bike_license.image' => 'bike license must be image',
                'id_card.image' => 'id card must be image',
                'house_card.image' => 'house card must be image',
                'bike_image.image' => 'bike image must be image',
            ];
            $validator = Validator::make($request->all(), $rules, $msg);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            if ($request->hasFile('agency_number')) {
                $agency_number = $this->saveImage($request->agency_number, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('bike_license')) {
                $bike_license = $this->saveImage($request->bike_license, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('id_card')) {
                $id_card = $this->saveImage($request->id_card, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('house_card')) {
                $house_card = $this->saveImage($request->house_card, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('bike_image')) {
                $bike_image = $this->saveImage($request->bike_image, 'uploads/drivers/documents', 'photo');
            }


            $user_id = Auth::user()->id;
            $storeNewDriverDoc = DriverDocuments::query()->updateOrCreate([
                'driver_id' => $user_id
            ], [
                'agency_number' => $agency_number,
                'bike_license' => $bike_license,
                'id_card' => $id_card,
                'house_card' => $house_card,
                'bike_image' => $bike_image,
                'status' => false,
            ]);

            if (isset($storeNewDriverDoc)) {
                return self::returnResponseDataApi(new DriverDocumentResource($storeNewDriverDoc), "تم تسجيل بيانات التوكتوك بنجاح في انتظار موافقة المشرفين", 200);
            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // registerDriverDoc


    public function checkDocument(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user->type === 'driver') {
                $checkDetails = DriverDetails::where('driver_id', $user->id)->first();
                $driverDocuments = DriverDocuments::where('driver_id', $user->id)->first();
                $data = [
                    'driver_details' => $checkDetails ? 1 : 0,
                    'driver_documents' => $driverDocuments ? 1 : 0,
                    'status' => $driverDocuments ? $driverDocuments->status : 0,
                ];
            } else {
                return self::returnResponseDataApi(null, 'المستخدم ليس سائق.', 403); // Return a 403 Forbidden status if the user is not a driver
            }

            return self::returnResponseDataApi($data, "تم استرداد البيانات بنجاح.", 200);
        } catch (\Exception $e) {
            return self::returnResponseDataApi(null, 'حدث خطأ. الرجاء معاودة المحاولة في وقت لاحق.', 500);
        }
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $user = User::find(Auth::user()->id);
        ($user->status == 1) ? $user->status = 0 : $user->status = 1;
        $user->save();
        if ($user->status == false) {
            return self::returnResponseDataApi(['status' => $user->status], "انت الان خارج الخدمة", 200);
        } else {
            return self::returnResponseDataApi(['status' => $user->status], "انت الان في الخدمة", 200);
        }
    } // change status

    public function updateDriverDetails(Request $request): JsonResponse
    {
        try {

            $rules = [
                'bike_type' => 'required',
                'bike_model' => 'required',
                'bike_color' => 'required',
                'area_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $user_id = Auth::user()->id;
            $storeNewDriverDetails = DriverDetails::query()
                ->where('driver_id', $user_id)->first();
            $storeNewDriverDetails->update([
                'bike_type' => $request->bike_type,
                'bike_model' => $request->bike_model,
                'bike_color' => $request->bike_color,
                'area_id' => $request->area_id,
                'driver_id' => $user_id
            ]);

            if ($storeNewDriverDetails->update()) {
                return self::returnResponseDataApi(new DriverResource($storeNewDriverDetails), "تم تحديث بيانات السائق بنجاح", 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // update driver details

    public function updateDriverDocument(Request $request): JsonResponse
    {
        try {
            $updateDriverDoc = DriverDocuments::query()
                ->where('driver_id', Auth::user()->id)
                ->firstOrFail();

            $rules = [
                'agency_number' => 'sometimes|image',
                'bike_license' => 'sometimes|image',
                'id_card' => 'sometimes|image',
                'house_card' => 'sometimes|image',
                'bike_image' => 'sometimes|image',
            ];

            $msg = [
                'agency_number.image' => 'agency number must be image',
                'bike_license.image' => 'bike license must be image',
                'id_card.image' => 'id card must be image',
                'house_card.image' => 'house card must be image',
                'bike_image.image' => 'bike image must be image',
            ];
            $validator = Validator::make($request->all(), $rules, $msg);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            if ($request->hasFile('agency_number')) {
                $agency_number = $this->saveImage($request->agency_number, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->agency_number)) {
                    unlink($updateDriverDoc->agency_number);
                }
            }
            if ($request->hasFile('bike_license')) {
                $bike_license = $this->saveImage($request->bike_license, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->bike_license)) {
                    unlink($updateDriverDoc->bike_license);
                }
            }
            if ($request->hasFile('id_card')) {
                $id_card = $this->saveImage($request->id_card, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->id_card)) {
                    unlink($updateDriverDoc->id_card);
                }
            }
            if ($request->hasFile('house_card')) {
                $house_card = $this->saveImage($request->house_card, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->house_card)) {
                    unlink($updateDriverDoc->house_card);
                }
            }
            if ($request->hasFile('bike_image')) {
                $bike_image = $this->saveImage($request->bike_image, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->bike_image)) {
                    unlink($updateDriverDoc->bike_image);
                }
            }

            $updateDriverDoc->update([
                'agency_number' => $agency_number ?? $updateDriverDoc->agency_number,
                'bike_license' => $bike_license ?? $updateDriverDoc->bike_license,
                'id_card' => $id_card ?? $updateDriverDoc->id_card,
                'house_card' => $house_card ?? $updateDriverDoc->house_card,
                'bike_image' => $bike_image ?? $updateDriverDoc->bike_image,
                'status' => false,
            ]);

            if (isset($updateDriverDoc)) {
                return self::returnResponseDataApi(new DriverDocumentResource($updateDriverDoc), "تم تحديث بيانات التوكتوك بنجاح في انتظار موافقة المشرفين", 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // update Driver Document


    public function startQuickTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'from_address' => 'required',
                'from_long' => 'required',
                'from_lat' => 'required',
                'to_address' => 'required',
                'to_long' => 'required',
                'to_lat' => 'required',
                'name' => 'required',
                'phone' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $checkQuickTrip = Trip::query()
                ->where('phone', $request->phone)
                ->where('ended', 0)
                ->latest()
                ->first();

            if ($checkQuickTrip) {
                return self::returnResponseDataApi(null, 'هناك رحلة حالية لم تنتهي بعد على نفس الرقم', 200, 200);
            }

            $createQuickTrip = Trip::query()->create([
                'from_address' => $request->from_address,
                'from_long' => $request->from_long,
                'from_lat' => $request->from_lat,
                'to_address' => $request->to_address,
                'to_long' => $request->to_long,
                'to_lat' => $request->to_lat,
                'name' => $request->name,
                'phone' => $request->phone,
                'driver_id' => Auth::id(),
                'type' => 'progress',
                'trip_type' => 'quick',
                'time_ride' => Carbon::now()
            ]);

            if ($createQuickTrip) {
                return self::returnResponseDataApi(new TripResource($createQuickTrip), "تم بدأ الرحلة الفورية بنجاح", 201, 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطأ ما أثناء دخول البيانات", 500);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    }

    public function endQuickTrip(Request $request): JsonResponse
    {
        $settigs = Setting::first(['vat', 'km']);
        try {
            $rules = [
                'distance' => 'required',
                'phone' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkQuickTrip = Trip::query()
                ->where('phone', '=', $request->phone)
                ->where('driver_id', '=', Auth::user()->id)
                ->where('ended', '=', 0)
                ->whereIn('type', ['new', 'accept', 'progress'])
                ->first();

            // $endTime = $checkQuickTrip->updated_at->diffInMinutes(Carbon::now());

            if ($checkQuickTrip) {
                $checkQuickTrip->time_arrive = Carbon::now();
                $checkQuickTrip->distance = $request->distance;
                $price = $checkQuickTrip->distance * $settigs->km;
                $vatTotal = $price * ($settigs->vat / 100);
                $total = $price - $vatTotal;
                $checkQuickTrip->price = $price;
                $checkQuickTrip->ended = true;
                $checkQuickTrip->type = 'complete';

                if ($checkQuickTrip->save()) {
                    $wallet = DriverWallet::query()
                        ->where('driver_id', '=', Auth::user()->id)
                        ->whereDay('day', '=', Carbon::now())
                        ->first();
                    if (!$wallet) {
                        $wallet = DriverWallet::query()
                            ->create([
                                'driver_id' => Auth::user()->id,
                                'day' => Carbon::now(),
                                'total' => $total,
                                'vat_total' => $vatTotal,
                            ]);
                    } else {
                        $wallet->total += $total;
                        $wallet->vat_total += $vatTotal;
                        $wallet->save();
                    }
                    return self::returnResponseDataApi(new TripResource($checkQuickTrip), "تم نهاية الرحلة الفورية بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 200);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يوجد رحلة حالية علي هذا الرقم", 200);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // endQuickTrip

    public function acceptTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'trip_id' => 'required',
                'long' => 'required',
                'lat' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $checkTrip = Trip::query()
                ->where('id', '=', $request->trip_id)
                ->where('ended', '=', 0)
                ->whereIn('type', ['new', 'reject']) // Using whereIn for multiple values
                ->whereNull('driver_id') // Check for null value
                ->first();

            if ($checkTrip) {
                $checkTrip->driver_id = Auth::user()->id;
                $checkTrip->type = 'accept';
                if ($checkTrip->save()) {

                    UserLocation::create([
                        'user_id' => auth()->user()->id,
                        'trip_id' => $checkTrip->id,
                        'long' => $request->long,
                        'lat' => $request->lat,
                    ]);

                    // send FCM
                    $data = [
                        'title' => 'تأكيد الرحلة',
                        'body' => 'تم تأكيد الرحلة من قبل سائق بنجاح',
                        'trip_id' => $checkTrip->id
                    ];
                    $this->sendFirebaseNotification($data, $checkTrip->user_id, 'acceptTrip', true);

                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم تاكيد الرحلة بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500);
                }
            } else {
                return self::returnResponseDataApi(null, "الرحلة غير متاحة أو تم قبولها بالفعل", 404);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    }


    public function cancelTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'trip_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkTrip = Trip::query()
                ->where('id', '=', $request->trip_id)
                ->where('ended', '=', 0)
                ->where('type', '=', 'accept')
                ->where('driver_id', '!=', null)
                ->first();

            if ($checkTrip) {
                $checkTrip->driver_id = null;
                $checkTrip->type = 'new';

                Notification::create([
                    'user_id' => $checkTrip->user_id,
                    'trip_id' => $request->trip_id,
                    'title' => 'الغاء الرحلة',
                    'description' => 'تم الغاء حجز الرحلة من السائق',
                    'type' => 'user',
                ]);
                if ($checkTrip->save()) {
                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم الغاء الرحلة بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يوجد رحلة فارغه بهذا المعرف", 200);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // cancel trip

    public function startTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'trip_id' => 'required',
                'long' => 'required',
                'lat' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            // Retrieve the trip details
            $checkTrip = Trip::where('id', $request->trip_id)
                ->where('ended', 0)
                ->where('type', 'accept')
                ->where('driver_id', Auth::id())
                ->first();

            // Check if the trip exists
            if (!$checkTrip) {
                return self::returnResponseDataApi(null, "لا توجد رحلة فارغة بهذا المعرف", 200);
            }

            // Retrieve user's location
            $userLocation = UserLocation::where('user_id', $checkTrip->user_id)
                ->where('trip_id', $request->trip_id)
                ->first();

            // Retrieve driver's location
            $driverLocation = UserLocation::where('user_id', Auth::id())
                ->where('trip_id', $request->trip_id)
                ->first();

            if ($driverLocation) {
                // Update driver's location
                $driverLocation->lat = $request->lat;
                $driverLocation->long = $request->long;
                $driverLocation->save();
            }

            // Check if both user and driver locations exist
            if ($userLocation && $driverLocation) {
                // Calculate distance between two points using Haversine formula
                $distance = $this->calculateDistance(
                    $userLocation->lat,
                    $userLocation->long,
                    $driverLocation->lat,
                    $driverLocation->long
                );

                // Define the maximum allowed distance for starting the trip (e.g., 1 km)
                $maxDistance = 1000; // in kilometers

                // Compare the distance with the maximum allowed distance
                if ($distance <= $maxDistance) {
                    // Update trip status to 'progress'
                    $checkTrip->type = 'progress';
                    // Set trip start time if not already set
                    if (!$checkTrip->time_ride) {
                        $checkTrip->time_ride = now();
                    }
                    $checkTrip->save();

                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم بدء الرحلة بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "أنت لست في مكان البداية المناسب", 200);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يمكن بدء الرحلة حاليًا. الرجاء التأكد من وجود مواقع صحيحة للمستخدم والسائق", 200);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    }


    // Function to calculate distance between two points using Haversine formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in kilometers

        // Convert latitude and longitude from degrees to radians
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        // Calculate the change in coordinates
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        // Apply Haversine formula
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        // Calculate the distance
        return $angle * $earthRadius;
    }


    public function endTrip(Request $request): JsonResponse
    {
        $settings = Setting::first(['vat', 'km']);

        try {
            $rules = [
                'trip_id' => 'required',
                'to_address' => 'nullable',
                'to_lat' => 'nullable',
                'to_long' => 'nullable',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $checkTrip = Trip::where('id', $request->trip_id)
                ->where('driver_id', Auth::user()->id)
                ->where('ended', 0)
                ->where('type', 'progress')
                ->first();

            if (!$checkTrip) {
                return self::returnResponseDataApi(null, "لا يوجد رحلة بهذا المعرف", 200);
            }

            $endTime = $checkTrip->updated_at->diffInMinutes(Carbon::now());
            $checkTrip->time_arrive = Carbon::now();

            if ($checkTrip->trip_type == 'without') {
                $fromLat = $checkTrip->from_lat;
                $fromLong = $checkTrip->from_long;
                $toLat = $request->to_lat;
                $toLong = $request->to_long;
                $checkTrip->to_long = $toLong;
                $checkTrip->to_lat = $toLat;
                $checkTrip->to_address = $request->to_address;
            } else {
                $fromLat = $checkTrip->from_lat;
                $fromLong = $checkTrip->from_long;
                $toLat = $checkTrip->to_lat;
                $toLong = $checkTrip->to_long;
            }

            $distance = $this->calculateDistance($fromLat, $fromLong, $toLat, $toLong);

            $price = $distance * $settings->km;
            $vatTotal = $price * ($settings->vat / 100);
            $total = $price - $vatTotal;

            $checkTrip->distance = $distance;
            $checkTrip->time = $endTime;
            $checkTrip->price = $price;
            $checkTrip->ended = true;
            $checkTrip->type = 'complete';

            if ($checkTrip->save()) {
                $wallet = DriverWallet::where('driver_id', Auth::user()->id)
                    ->whereDay('day', Carbon::now())
                    ->first();

                if (!$wallet) {
                    $wallet = DriverWallet::create([
                        'driver_id' => Auth::user()->id,
                        'day' => Carbon::now(),
                        'total' => $total,
                        'vat_total' => $vatTotal,
                    ]);
                } else {
                    $wallet->total += $total;
                    $wallet->vat_total += $vatTotal;
                    $wallet->save();
                }

                Notification::create([
                    'user_id' => $checkTrip->user_id,
                    'trip_id' => $request->trip_id,
                    'title' => 'انتهاء الرحلة',
                    'description' => 'تم الانتهاء من الرحلة',
                    'type' => 'user',
                ]);

                return self::returnResponseDataApi(new TripResource($checkTrip), "تم نهاية الرحلة بنجاح", 201, 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 200);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    }

    public function driverAllTrip(Request $request): JsonResponse
    {
        try {
            if ($request->has('type')) {
                if ($request->type == 'reject') {
                    $trips = Trip::query()
                        ->where('driver_id', '=', Auth::user()->id)
                        ->where('type', '=', 'reject')
                        ->where('ended', '=', 0)
                        ->orderBy('created_at', 'DESC')
                        ->latest()->get();
                    $data = $trips;
                } elseif ($request->type == 'complete') {
                    $trips = Trip::query()
                        ->where('driver_id', '=', Auth::user()->id)
                        ->where('type', '=', 'complete')
                        ->where('ended', '=', 1)
                        ->orderBy('created_at', 'DESC')
                        ->latest()->get();
                    $data = $trips;
                } else {
                    $driverId = auth()->user()->id;

                    $existingTrip = Trip::where('driver_id', $driverId)->where('ended', 0)->exists();

                    if ($existingTrip) {
                        $data = [];
                    } else {
                        $trips = Trip::query()
                            ->where('type', '=', 'new')
                            ->where('trip_type', '!=', 'scheduled')
                            ->where('ended', '=', 0)
                            ->whereDay('created_at', '=', Carbon::now())
                            ->orderBy('created_at', 'DESC')
                            ->latest()
                            ->get();

                        $data = $trips;
                    }

                }

                if (count($data) > 0) {
                    return self::returnResponseDataApi(TripResource::collection($data), 'تم الحصول علي جميع بيانات الرحلات بنجاح', 200, 200);
                } else {
                    return self::returnResponseDataApi($data, 'عفوا لا يوجد رحلات حاليا', 200, 200);
                }
            } else {
                return self::returnResponseDataApi(null, 'يرجي ادخال النوع', 422, 422);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // get all trip by filter

    public function driverWallet(): JsonResponse
    {
        $vat = Setting::select('vat')->first()->vat;
        $driver = Auth::user();
        $wallet['vat_total'] = DriverWallet::query()
            ->where('driver_id', '=', $driver->id)
            ->where('status', '=', false)
            ->sum('vat_total');

        $trips = Trip::query()
            ->select(['id', 'price', 'updated_at'])
            ->where('driver_id', '=', $driver->id)
            ->where('type', '=', 'complete')
            ->where('created_at', '>=', Carbon::now()->subDays(7)) // Get trips from the last 7 days
            ->where('ended', '=', true)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($trips as $key => $trip) {
            $wallet['trips'][$key]['id'] = $trip->id;
            $wallet['trips'][$key]['vat'] = $trip->price * ($vat / 100);
            $wallet['trips'][$key]['time_arrive'] = $trip->updated_at->format('Y-m-d H:i:s');
        }

        if (!$wallet['vat_total']) {
            return self::returnResponseDataApi(null, 'لم يتم انشاء محفظة حتي الان قم باتمام اول رحلة لفتح المحفظة', 200);
        }
        return self::returnResponseDataApi($wallet, 'تم الحصول علي بيانات المحفظة بنجاح', 200);
    } // driverWallet

    public function driverProfit(Request $request): JsonResponse
    {
        try {
            $setting = Setting::first(['vat', 'km']);
            $driver = Auth::user();
            $toDay = Carbon::yesterday()->format('Y-m-d');
            $lastWeek = Carbon::now()->subDays(7)->format('Y-m-d');
            $from = Carbon::parse($request->from)->format('Y-m-d');
            $to = Carbon::parse($request->to)->format('Y-m-d');

            if ($request->type == null) {
                return self::returnResponseDataApi(null, 'يرجي ادخال النوع', 422, 422);
            } elseif ($request->type == 'day') {
                $trip = Trip::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('type', '=', 'complete')
                    ->where('ended', '=', true)
                    ->whereDate('updated_at', '=', $toDay);

                $wallet = DriverWallet::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('status', '=', false)
                    ->whereDate('day', '=', $toDay);

                $profit['trips_count'] = $trip->count();
                $profit['total_trips_price'] = $trip->sum('price');
                $profit['trips_distance'] = $trip->sum('distance');
                $profit['km_price'] = $setting->km;
                $profit['total'] = $profit['total_trips_price'];
                $profit['vat_total'] = $wallet->sum('vat_total');
                $profit['net_total'] = $profit['total'] - $profit['vat_total'];
            } elseif ($request->type == 'week') {
                $trip = Trip::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('type', '=', 'complete')
                    ->where('ended', '=', 1)
                    // ->whereDate('updated_at', '<=', $toDay)
                    ->whereBetween('updated_at', [$toDay, $lastWeek])->get();
                // ->whereDate('updated_at', '>', $lastWeek)->get();

                $wallet = DriverWallet::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('status', '=', false)
                    ->whereDate('day', '<=', $toDay)
                    ->whereDate('day', '>=', $lastWeek);

                $profit['trips_count'] = $trip->count();
                $profit['total_trips_price'] = $trip->sum('price');
                $profit['trips_distance'] = $trip->sum('distance');
                $profit['km_price'] = $setting->km;
                $profit['total'] = $profit['total_trips_price'];
                $profit['vat_total'] = $wallet->sum('vat_total');
                $profit['net_total'] = $profit['total'] - $profit['vat_total'];
                $profit['from'] = $lastWeek;
                $profit['to'] = $toDay;


                // Retrieve wallet days as before
                $walletDays = DriverWallet::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('status', '=', false)
                    ->whereDate('day', '<=', $toDay)
                    ->whereDate('day', '>=', $lastWeek)
                    ->groupBy('day')
                    ->select('day', DB::raw('SUM(total) as total_amount'))
                    ->get();

                // Create a collection to store all dates
                $dates = collect();

                // If there are wallet days
                if ($walletDays->isNotEmpty()) {
                    // Get all dates with recorded wallet days
                    $walletDates = $walletDays->pluck('day');

                    // Add the recorded wallet days to the collection
                    $dates = $dates->merge($walletDates);
                }

                // Create Carbon instances for the date range from lastWeek to today
                $currentDay = Carbon::parse($lastWeek);
                $toDayCarbon = Carbon::parse($toDay);

                // Add all days between lastWeek and today to the collection
                while ($currentDay->lte($toDayCarbon)) {
                    $dates->push($currentDay->toDateString());
                    $currentDay->addDay();
                }

                // Filter the dates to include only unique values
                $dates = $dates->unique();

                // Create a new array to store the unique dates
                $uniqueDates = [];

                // Populate the $uniqueDates array with the appropriate data
                foreach ($dates as $date) {
                    $walletDay = $walletDays->where('day', $date)->first();
                    $uniqueDates[$date] = [
                        'price' => $walletDay ? $walletDay->total_amount : 0,
                        'day' => $date,
                        'day_name' => Carbon::parse($date)->format('l')
                    ];
                }

                // Check if the 'trips' key exists in $profit array
                if (!array_key_exists('trips', $profit)) {
                    // Initialize 'trips' key as an empty array if it doesn't exist
                    $profit['trips'] = [];
                }

                // Merge the $uniqueDates array with the existing $profit['trips'] array
                $profit['trips'] = array_merge($uniqueDates, $profit['trips']);

                // Sort the trips by day
                usort($profit['trips'], function ($a, $b) {
                    return strtotime($a['day']) - strtotime($b['day']);
                });
            } elseif ($request->type == 'custom') {
                if ($request->from == null || $request->to == null) {
                    return self::returnResponseDataApi(null, 'يرجي ادخال التاريخ من والى', 422);
                } else {
                    $trip = Trip::query()
                        ->where('driver_id', '=', $driver->id)
                        ->where('type', '=', 'complete')
                        ->where('ended', '=', true)
                        ->whereDate('updated_at', '<=', $to)
                        ->whereDate('updated_at', '>=', $from);

                    $wallet = DriverWallet::query()
                        ->where('driver_id', '=', $driver->id)
                        ->where('status', '=', false)
                        ->whereDate('day', '<=', $to)
                        ->whereDate('day', '>=', $from);

                    $profit['trips_count'] = $trip->count();
                    $profit['total_trips_price'] = $trip->sum('price');
                    $profit['trips_distance'] = $trip->sum('distance');
                    $profit['km_price'] = $setting->km;
                    $profit['total'] = $profit['total_trips_price'];
                    $profit['vat_total'] = $wallet->sum('vat_total');
                    $profit['net_total'] = $profit['total'] - $profit['vat_total'];
                }
            }

            return self::returnResponseDataApi($profit, 'تم الحصول علي بيانات الارباح بنجاح', 200);
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    } // driverProfit

    public function getInfoDriver()
    {
        try {
            $driver_id = auth()->user()->id;

            $sliders = Slider::query()
                ->select('image', 'link')
                ->where('type', 'driver')
                ->where('status', true)
                ->get();

            $sliderData = [];
            foreach ($sliders as $slider) {
                $sliderData[] = [
                    'image' => 'https://hero.topbusiness.io/' . $slider->image,
                    'link' => $slider->link,
                ];
            }

            $trips = Trip::where('driver_id', $driver_id)
                ->whereIn('type', ['accept', 'progress'])
                ->first();
            $driver_status = User::where('id', $driver_id)->pluck('status')->first();
            $driver_documents = DriverDocuments::where('driver_id', $driver_id)->first();
            $driver_details = DriverDetails::where('driver_id', $driver_id)->first();

            $datails = [
                'sliders' =>  $sliderData,
                'driver_id' => $driver_id,
                'trip' => $trips,
                'driver_status' => $driver_status,
                'city_id' => $driver_details->area->city_id,
                'driver_details' => $driver_details,
                'driver_documents' => [
                    'agency_number' => 'https://hero.topbusiness.io/' . $driver_documents->agency_number,
                    'bike_license' => 'https://hero.topbusiness.io/' . $driver_documents->bike_license,
                    'id_card' => 'https://hero.topbusiness.io/' . $driver_documents->id_card,
                    'house_card' => 'https://hero.topbusiness.io/' . $driver_documents->house_card,
                    'bike_image' => 'https://hero.topbusiness.io/' . $driver_documents->bike_image,
                ],
            ];

            return self::returnResponseDataApi($datails, 'تم الحصول على بيانات السائق بنجاح', 200);
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    }

    public function getTripStatus(): JsonResponse
    {
        try {
            $id = auth()->user()->id;
            $tripStatus = Trip::where(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->orWhere('driver_id', $id);
            })
                ->where('trip_type', '!=', 'scheduled')
                ->with('user')
                ->with('driver')
                ->latest()
                ->first();

            return self::returnResponseDataApi($tripStatus, 'تم الحصول على بيانات حالة الرحلة بنجاح', 200);
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, 500);
        }
    }

    public function driverLocation(Request $request): JsonResponse
    {
        try {
            $rules = [
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            $driverId = Auth::id();

            UserLocation::updateOrCreate(
                [
                    'driver_id' => $driverId
                ],
                [
                    'driver_id' => $driverId,
                    'lat' => $request->input('lat'),
                    'long' => $request->input('long')
                ]
            );

            return response()->json(['message' => 'تم تحديث موقع السائق بنجاح', 'code' => 200], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
