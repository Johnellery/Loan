<?php

use App\Models\Philprovince;
use App\Models\Philmuni;
use App\Models\Philbrgy;
$philprovinces = Philprovince::all();
$philcities = Philmuni::all();
$philbarangays = Philbrgy::all();

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">

            <div class="max-w-3xl mx-auto px-4 py-8">
                <div class="bg-white rounded-lg shadow-lg p-6">

                    <form action="/create" method = "POST">
                        @csrf
                        <input type="hidden" name="bike_id" value="{{ request('bike_id') }}">
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="branch_id" value="{{ auth()->user()->branch->id }}">
                        <input type="hidden" name="contact" value="{{ auth()->user()->phone }}">
                        <div class="border-b border-gray-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-gray-900">Requirements</h2>
                            <p class="mt-1 text-sm leading-6 text-gray-600">Please provide your requirements below</p>

                            <div class="sm:col-span-2">
                                <label for="picture" class="block text-sm font-medium leading-6 text-gray-900">Picture 2x2</label>
                                <div class="mt-3">
                                    <div class="relative rounded-md shadow-sm">
                                        <label for="picture" class="cursor-pointer block text-gray-600 border border-gray-300 hover:text-gray-900 hover:border-gray-400 rounded-md px-3 py-2 transition ease-in-out duration-150">
                                            <span id="picture-label" class="block text-center">Choose a file</span>
                                            <input required type="file" name="picture" id="picture" class="hidden" accept="image/*" onchange="displaySelectedImage(this, 'picture-preview', 'picture-label')">
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <img id="picture-preview" class="hidden max-h-40 mx-auto" alt="Selected Image">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="file" class="block text-sm font-medium leading-6 text-gray-900">Valid ID</label>
                                <div class="mt-3">
                                    <div class="relative rounded-md shadow-sm">
                                        <label for="file" class="cursor-pointer block text-gray-600 border border-gray-300 hover:text-gray-900 hover:border-gray-400 rounded-md px-3 py-2 transition ease-in-out duration-150">
                                            <span id="file-label" class="block text-center">Choose a file</span>
                                            <input required type="file" name="file" id="file" class="hidden" accept="image/*" onchange="displaySelectedImage(this, 'file-preview', 'file-label')">
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <img id="file-preview" class="hidden max-h-40 mx-auto" alt="Selected Image">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="clearance" class="block text-sm font-medium leading-6 text-gray-900">Barangay Clearance</label>
                                <div class="mt-3">
                                    <div class ="relative rounded-md shadow-sm">
                                        <label for="clearance" class="cursor-pointer block text-gray-600 border border-gray-300 hover:text-gray-900 hover:border-gray-400 rounded-md px-3 py-2 transition ease-in-out duration-150">
                                            <span id="clearance-label" class="block text-center">Choose a file</span>
                                            <input  required type="file" name="clearance" id="clearance" class="hidden" accept="image/*" onchange="displaySelectedImage(this, 'clearance-preview', 'clearance-label')">
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <img id="clearance-preview" class="hidden max-h-40 mx-auto" alt="Selected Image">
                                </div>
                            </div>


                        </div>
                        <div class="border-b border-gray-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-gray-900">Personal Information</h2>
                            <p class="mt-1 text-sm leading-6 text-gray-600">Please provide your personal information below</p>





                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                  <label for="first" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                                  <div class="mt-3">
                                    <input required placeholder="Enter your First name"type="text" name="first" id="first" autocomplete="first" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    @error('first')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="middle" class="block text-sm font-medium leading-6 text-gray-900">Middle name</label>
                                    <div class="mt-3">
                                      <input required placeholder="Enter your Middle name"type="text" name="middle" id="middle" autocomplete="middle" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                  </div>
                                <div class="sm:col-span-2">
                                  <label for="last" class="block text-sm font-medium leading-6 text-gray-900">Last name</label>
                                  <div class="mt-3">
                                    <input required placeholder="Enter your Last name" type="text" name="last" id="last" autocomplete="last" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                  </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="age" class="block text-sm font-medium leading-6 text-gray-900">Age</label>
                                    <div class="mt-3">
                                        <input required pattern="\d+" title="Please enter a valid number" placeholder="Enter your Age" type="text"
                                               name="age" id="age" autocomplete="age"
                                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="gender" class="block text-sm font-medium leading-6 text-gray-900">Gender</label>
                                    <div class="mt-3 relative rounded-md shadow-sm">
                                        <select required placeholder="Enter your Gender" id="gender" name="gender" class="block w-full pl-3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option disabled selected>Choose a Gender</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="civil" class="block text-sm font-medium leading-6 text-gray-900">Civil Status</label>
                                    <div class="mt-3 relative rounded-md shadow-sm">
                                        <select required  id="civil" name="civil" class="block w-full pl-3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option disabled selected>Choose a Civil status</option>
                                            <option>Single</option>
                                            <option>Married</option>
                                            <option>Divorced</option>
                                            <option>Widowed</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="religion" class="block text-sm font-medium leading-6 text-gray-900">Religion</label>
                                    <div class="mt-3">
                                        <input required placeholder="Enter your Religion" type="text" name="religion" id="religion" autocomplete="religion" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="occupation" class="block text-sm font-medium leading-6 text-gray-900">Occupation</label>
                                    <div class="mt-3">
                                        <input required placeholder="Enter your Occupation" type="text" name="occupation" id="occupation" autocomplete="occupation" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="spouse" class="block text-sm font-medium leading-6 text-gray-900">Spouse (Optional) </label>
                                    <div class="mt-3">
                                        <input placeholder="Enter your Spouse (Optional)" type="text" name="spouse" id="spouse" autocomplete="spouse" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="occupation_spouse" class="block text-sm font-medium leading-6 text-gray-900">Spouse Occupation (Optional) </label>
                                    <div class="mt-3">
                                        <input  placeholder="Enter your Spouse Occupation (Optional)" type="text" name="occupation_spouse" id="occupation_spouse" autocomplete="occupation_spouse" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="contact_spouse" class="block text-sm font-medium leading-6 text-gray-900">Spouse Contact (Optional) </label>
                                    <div class="mt-3">
                                        <input placeholder="Enter your Spouse Contact (Optional)" type="text" name="contact_spouse" id="contact_spouse" autocomplete="contact_spouse" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b p-3 border-gray-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-gray-900">Address</h2>
                            <p class="mt-1 text-sm leading-6 text-gray-600">Please use your permanent address</p>


                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="province" class="block text-sm font-medium leading-6 text-gray-900">Province</label>
                                <div class="mt-3 relative rounded-md shadow-sm">
                                    <select id="province" name="province" class="block w-full pl-3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option disabled selected>Choose a Province</option>
                                        @foreach($philprovinces as $philprovince)
                                            <option value="{{ $philprovince->provDesc }}">{{ $philprovince->provDesc }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"></div>
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City/Municipality</label>
                                <div class="mt-3 relative rounded-md shadow-sm">
                                    <select id="city" name="city" class="block w-full pl-3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option disabled selected>Choose a City/Municipality</option>
                                        @foreach($philcities as $philcity)
                                        <option value="{{ $philcity->citymunDesc}}">{{$philcity->citymunDesc }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"></div>
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="barangay" class="block text-sm font-medium leading-6 text-gray-900">Barangay</label>
                                <div class="mt-3 relative rounded-md shadow-sm">
                                    <select id="barangay" name="barangay" class="block w-full pl-3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option disabled selected>Choose a Barangay</option>
                                        @foreach($philbarangays as $philbarangay)
                                            <option value="{{ $philbarangay->brgyDesc }}">{{ $philbarangay->brgyDesc }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"></div>
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="unit" class="block text-sm font-medium leading-6 text-gray-900">Unit no., floor, building, street</label>
                                <div class="mt-3">
                                    <input placeholder="Enter the Unit no., floor, building, street" type="text" name="unit" id="unit" autocomplete="unit" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="border-b p-3 border-gray-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-gray-900">Loan Details</h2>
                            <p class="mt-1 text-sm leading-6 text-gray-600">Please select your desire loan term</p>


                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                <div class="sm:col-span-3">
                                    <label for="installment" class="block text-sm font-medium leading-6 text-gray-900">Installment</label>
                                    <div class="mt-3 relative rounded-md shadow-sm">
                                        <select id="installment" name="installment" class="block w-full pl-3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option disabled selected>Choose a Payment type</option>
                                            <option value="4">Weekly</option>
                                            <option value="1">Monthly</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="term" class="block text-sm font-medium leading-6 text-gray-900">Loan Term</label>
                                    <div class="mt-3 relative rounded-md shadow-sm">
                                        <select id="term" name="term" class="block w-full pl-3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option disabled selected>Choose a Loan term</option>
                                            <option value="4">4 Months</option>
                                            <option value="5">5 Months</option>
                                            <option value="6">6 Months</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        </div>
                                    </div>
                                </div>

                        </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <button type="button" class="text-sm font-semibold leading-6 text-gray-900" id="cancelButton">
                                <a href="{{ route('livewire.newcart') }}">Cancel</a>
                            </button>
                            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
    </html>
    <script>

        function displaySelectedImage(input, previewId, labelId) {
            const fileLabel = document.getElementById(labelId);
            const imagePreview = document.getElementById(previewId);

            if (input.files.length > 0) {
                const file = input.files[0];
                const objectURL = URL.createObjectURL(file);

                fileLabel.textContent = file.name;
                imagePreview.src = objectURL;
                imagePreview.classList.remove('hidden');
            } else {
                fileLabel.textContent = 'Choose a file';
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
            }
        }

        document.getElementById('cancelButton').addEventListener('click', function() {
        window.location.href = '{{ url('/customer/home') }}';
            });
    </script>




    {{-- <div class="border-b border-gray-900/10 pb-12">
        <h2 class="text-base font-semibold leading-7 text-gray-900">Personal Information</h2>
        <p class="mt-1 text-sm leading-6 text-gray-600">Use a permanent address where you can receive mail.</p>

        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="first-name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
            <div class="mt-2">
              <input type="text" name="first-name" id="first-name" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="last-name" class="block text-sm font-medium leading-6 text-gray-900">Last name</label>
            <div class="mt-2">
              <input type="text" name="last-name" id="last-name" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="sm:col-span-4">
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
            <div class="mt-2">
              <input id="email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
            <div class="mt-2">
              <select id="country" name="country" autocomplete="country-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                <option>United States</option>
                <option>Canada</option>
                <option>Mexico</option>
              </select>
            </div>
          </div>

          <div class="col-span-full">
            <label for="street-address" class="block text-sm font-medium leading-6 text-gray-900">Street address</label>
            <div class="mt-2">
              <input type="text" name="street-address" id="street-address" autocomplete="street-address" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="sm:col-span-2 sm:col-start-1">
            <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
            <div class="mt-2">
              <input type="text" name="city" id="city" autocomplete="address-level2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="sm:col-span-2">
            <label for="region" class="block text-sm font-medium leading-6 text-gray-900">State / Province</label>
            <div class="mt-2">
              <input type="text" name="region" id="region" autocomplete="address-level1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="sm:col-span-2">
            <label for="postal-code" class="block text-sm font-medium leading-6 text-gray-900">ZIP / Postal code</label>
            <div class="mt-2">
              <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>
        </div>
      </div> --}}
