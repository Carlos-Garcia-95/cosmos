@vite(['resources/css/pago.css'])

@php
    // Para mantener los valores introducidos
    $input_antiguo = [
        'cardNumber' => old('cardNumber'),
        'cardName' => old('cardName'),
        'cardMonth' => old('cardMonth'),
        'cardYear' => old('cardYear'),
        'cardCvv' => old('cardCvv'),
    ];
@endphp

{{-- Pass old input to JavaScript so Vue can pick it up --}}
<script>
    window.laravel_antiguo_input = @json($input_antiguo);
    // This flag tells your JS to open the modal if payment errors exist
    window.abrir_modalpago_sierrores = {{ $errors->getBag('procesar_pago')->any() ? 'true' : 'false' }};
</script>

<div class='modal_pago hidden' id='modal_pago'>
    <div class='container_pago' id='container_pago'>
        <form method='POST' action='{{ route('procesar_pago') }}' id='procesar_pago_form'>
            @csrf

            <div class="wrapper" id="app">
                <div class="card-form">
                    <div class="card-list">
                        <div class="card-item" v-bind:class="{ '-active' : isCardFlipped }">
                            <div class="card-item__side -front">
                                <div class="card-item__focus" v-bind:class="{'-active' : focusElementStyle }" v-bind:style="focusElementStyle" ref="focusElement"></div>
                                <div class="card-item__cover">
                                    <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + currentCardBackground + '.jpeg'" class="card-item__bg">
                                </div>

                                <div class="card-item__wrapper">
                                    <div class="card-item__top">
                                        <img src="https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/chip.png" class="card-item__chip">
                                        <div class="card-item__type">
                                            <transition name="slide-fade-up">
                                                <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + getCardType + '.png'" v-if="getCardType" v-bind:key="getCardType" alt="" class="card-item__typeImg">
                                            </transition>
                                        </div>
                                    </div>
                                    <label for="cardNumber" class="card-item__number" ref="cardNumber">
                                        <template v-if="getCardType === 'amex'">
                                            <span v-for="(n, $index) in amexCardMask" :key="$index">
                                                <transition name="slide-fade-up">
                                                    <div class="card-item__numberItem" v-if="$index > 4 && $index < 14 && cardNumber.length > $index && n.trim() !== ''">*</div>
                                                    <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" :key="$index" v-else-if="cardNumber.length > $index">
                                                        @{{cardNumber[$index]}}
                                                    </div>
                                                    <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" v-else :key="$index + 1">@{{n}}</div>
                                                </transition>
                                            </span>
                                        </template>

                                        <template v-else>
                                            <span v-for="(n, $index) in otherCardMask" :key="$index">
                                                <transition name="slide-fade-up">
                                                    <div class="card-item__numberItem" v-if="$index > 4 && $index < 15 && cardNumber.length > $index && n.trim() !== ''">*</div>
                                                    <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" :key="$index" v-else-if="cardNumber.length > $index">
                                                        @{{cardNumber[$index]}}
                                                    </div>
                                                    <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" v-else :key="$index + 1">@{{n}}</div>
                                                </transition>
                                            </span>
                                        </template>
                                    </label>
                                    <div class="card-item__content">
                                        <label for="cardName" class="card-item__info" ref="cardName">
                                            <div class="card-item__holder">TITULAR</div>
                                            <transition name="slide-fade-up">
                                                <div class="card-item__name" v-if="cardName.length" key="1">
                                                    <transition-group name="slide-fade-right">
                                                        <span class="card-item__nameItem" v-for="(n, $index) in cardName.replace(/\s\s+/g, ' ')" v-if="$index === $index" v-bind:key="$index + 1">@{{n}}</span>
                                                    </transition-group>
                                                </div>
                                                <div class="card-item__name" v-else key="2">Titular</div>
                                            </transition>
                                        </label>
                                        <div class="card-item__date" ref="cardDate">
                                            <label for="cardMonth" class="card-item__dateTitle">Caducidad</label>
                                            <label for="cardMonth" class="card-item__dateItem">
                                                <transition name="slide-fade-up">
                                                    <span v-if="cardMonth" v-bind:key="cardMonth">@{{cardMonth}}</span>
                                                    <span v-else key="2">MM</span>
                                                </transition>
                                            </label>
                                             / &nbsp;
                                            <label for="cardYear" class="card-item__dateItem">
                                                <transition name="slide-fade-up">
                                                    <span v-if="cardYear" v-bind:key="cardYear">@{{String(cardYear).slice(2,4)}}</span>
                                                    <span v-else key="2">YY</span>
                                                </transition>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-item__side -back">
                                <div class="card-item__cover">
                                    <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + currentCardBackground + '.jpeg'" class="card-item__bg">
                                </div>
                                <div class="card-item__band"></div>
                                <div class="card-item__cvv">
                                    <div class="card-item__cvvTitle">CVV</div>
                                    <div class="card-item__cvvBand">
                                        <span v-for="(n, $index) in cardCvv" :key="$index">
                                            *
                                        </span>

                                    </div>
                                    <div class="card-item__type">
                                        <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + getCardType + '.png'" v-if="getCardType" class="card-item__typeImg">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-form__inner">
                        <div class="card-input">
                            <label for="cardNumber" class="card-input__label">Nº Tarjeta</label>
                            <input type="text" name="cardNumber" id="cardNumber" class="card-input__input" v-mask="generateCardNumberMask" v-model="cardNumber" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardNumber" placeholder='#### #### #### ####' autocomplete="off" required>
                        </div>
                        <div class="card-input">
                            <label for="cardName" class="card-input__label">Titular de la Tarjeta</label>
                            <input type="text" name="cardName" id="cardName" class="card-input__input" v-model="cardName" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardName" placeholder='Titular' autocomplete="off" required>
                        </div>
                        <div class="card-form__row">
                            <div class="card-form__col">
                                <div class="card-form__group">
                                    <label for="cardMonth" class="card-input__label">Fecha de Caducidad</label>
                                    <select class="card-input__input -select" name="cardMonth" id="cardMonth" v-model="cardMonth" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardDate" required>
                                        <option value="" disabled selected>Mes</option>
                                        <option v-bind:value="n < 10 ? '0' + n : n" v-for="n in 12" v-bind:disabled="n < minCardMonth" v-bind:key="n">
                                            @{{n < 10 ? '0' + n : n}}
                                        </option>
                                    </select>
                                    <select class="card-input__input -select" name="cardYear" id="cardYear" v-model="cardYear" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardDate" required>
                                        <option value="" disabled selected>Año</option>
                                        <option v-bind:value="$index + minCardYear" v-for="(n, $index) in 12" v-bind:key="n">
                                            @{{$index + minCardYear}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-form__col -cvv">
                                <div class="card-input">
                                    <label for="cardCvv" class="card-input__label">CVV</label>
                                    <input type="text" class="card-input__input" name="cardCvv" id="cardCvv" v-mask="'####'" maxlength="4" v-model="cardCvv" v-on:focus="flipCard(true)" v-on:blur="flipCard(false)" placeholder='123' autocomplete="off" required>
                                </div>
                            </div>
                        </div>

                        <div id='asientos_div'></div>
                        <div id='datos_sesion_div'></div>
                        <div id='precio_div'></div>

                        @if ($errors->getBag('procesar_pago')->any())
                            <div class='errores_pago' id='errores_pago'>
                                @foreach ($errors->getBag('procesar_pago')->all() as $error)
                                    <p class='error-text text-center'>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        

                        <button type='submit' class="card-form__button">
                            Confirmar Pago
                        </button>
                        <button type="button" class="boton_volver_pago" id='boton_volver_pago' @click="cancelarPago">
                            Cancelar Compra
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Abre el modal de nuevo si hay algún error en el pago 
    document.addEventListener('DOMContentLoaded', function() {
        if (window.abrir_modalpago_sierrores) {
            const datos_sesion = JSON.parse(localStorage.getItem('datos_sesion'));
            const asientos = JSON.parse(localStorage.getItem('asientos'));
            console.log(datos_sesion);
            console.log(asientos);
            
            if (typeof mostrar_modal_pago === 'function') {
                mostrar_modal_pago(datos_sesion, asientos);
            } else {
                console.error('La función mostrar_modal_pago no se encontró');
            }
        }
    });
</script>