<!-- Validation douce : Warning si adresse principale trop longue -->
<p
    v-if="address && address.length > 38"
    class="mb-4 text-xs text-orange-500"
>
    ⚠️ @lang('shop::app.customers.account.addresses.edit.address-warning')
</p>

<!-- Address 2 (Complément d'adresse) - Customer Account Edit -->
<x-shop::form.control-group>
    <x-shop::form.control-group.label>
        @lang('shop::app.customers.account.addresses.edit.address2')
    </x-shop::form.control-group.label>

    <x-shop::form.control-group.control
        type="text"
        name="address2"
        :value="old('address2') ?? $address->address2"
        rules="address"
        :label="trans('shop::app.customers.account.addresses.edit.address2')"
        :placeholder="trans('shop::app.customers.account.addresses.edit.address2')"
        v-model="address2Field"
    />

    <x-shop::form.control-group.error control-name="address2" />
</x-shop::form.control-group>
