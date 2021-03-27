<template>
    <div class="md-layout">
        <label class="md-layout-item md-form-label" :class="{['md-size-'+labelSize]: true}">
            {{ label }}
            ({{ currencyUnit }})
        </label>
        <div class="md-layout-item">
            <md-field class="md-invalid">
                <md-input @input="reformatInput($event)" :value="currencyFormatedValue" :disabled="disabled" />
            </md-field>
            <span v-if="value > 0">
                {{ digitsToWords(inputValue) }} {{ currencyUnit }}
            </span>
        </div>
    </div>
</template>

<script>
import { priceFilterMixin } from '@/mixins/Mixins'
export default {
    name: 'PriceInput',
    mixins: [priceFilterMixin],
    watch: {
        value: {
            immediate: true,
            handler(value) {
                this.inputValue = value;
            }
        }
    },
    computed: {
        currencyFormatedValue () {
            return this.convertToCurrencyFormat(this.inputValue)
        }
    },
    data: () => {
        return {
            inputValue: null
        }
    },
    props: {
        value: {
            default: 0,
            type: Number
        },
        label: {
            default: '',
            type: String
        },
        labelSize: {
            default: 15,
            type: Number
        },
        disabled: {
            default: false,
            type: Boolean
        }
    },
    methods: {
        reformatInput (data) {
            data = data.replace(/٬/g, '')
            data = this.toEnDigit(data)
            this.inputValue = data
            this.$emit('input', data)
        }
    }
}
</script>

<style scoped>

</style>
