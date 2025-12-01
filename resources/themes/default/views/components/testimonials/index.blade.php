@props(['testimonials' => []])

<div class="my-16">
    <v-testimonials :testimonials="{{ json_encode($testimonials) }}">
        <div class="overflow-hidden">
            <div class="shimmer h-64 w-full"></div>
        </div>
    </v-testimonials>
</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-testimonials-template"
    >
        <div class="container mx-auto px-4 max-w-5xl mt-16" v-if="testimonials && testimonials.length > 0">
            <!-- Quote Icon -->
            <div class="flex justify-center mb-8">
                <svg class="w-12 h-12" fill="#CBD5DF"  viewBox="0 0 32 32">
                    <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                </svg>
            </div>



            <!-- Testimonials Container -->
            <div class="relative overflow-hidden" ref="sliderContainer">
                <div
                    class="inline-flex transition-transform duration-500 ease-in-out"
                    :style="sliderStyle"
                    ref="track"
                >
                    <!-- Testimonial Slide -->
                    <div
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        class="text-center px-4 md:px-12"
                        :style="{ width: containerWidth + 'px' }"
                        ref="slide"
                    >
                        <!-- Rating Stars -->
                        <div class="flex justify-center mb-4" v-if="testimonial.rating">
                            <span
                                v-for="star in 5"
                                :key="star"
                                class="text-2xl"
                                :class="star <= testimonial.rating ? 'text-yellow-400' : 'text-gray-300'"
                            >
                                ★
                            </span>
                        </div>

                        <!-- Comment -->
                        <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                            @{{ testimonial.comment }}
                        </p>

                        <!-- Separator -->


                        <div class="flex justify-center mb-4">
                            <div style="height: 2px; width: 48px; background-color: #F6F2EB; border-radius: 9999px;"></div>
                        </div>


                        <!-- Name & Position -->
                        <p class="font-semibold text-gray-900">@{{ testimonial.name }}</p>
                        <p class="text-sm text-gray-500" v-if="testimonial.position">
                            @{{ testimonial.position }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation Dots -->
            <div class="flex justify-center mt-8 space-x-2" v-if="testimonials.length > 1">
                <button
                    v-for="(testimonial, index) in testimonials"
                    :key="index"
                    @click="goTo(index)"
                    class="dot w-3 h-3 rounded-full transition-all duration-300"
                    :class="index === currentIndex ? 'bg-blue-500' : 'bg-gray-300'"
                    :aria-label="`Testimonial ${index + 1}`"
                ></button>
            </div>
        </div>
    </script>

    <script type="module">
        app.component("v-testimonials", {
            template: '#v-testimonials-template',

            props: ['testimonials'],

            data() {
                return {
                    currentIndex: 0,
                    autoPlayInterval: null,
                    autoPlayDelay: 5000,
                    containerWidth: 0,
                };
            },

            computed: {
                sliderStyle() {
                    return {
                        transform: `translateX(-${this.currentIndex * this.containerWidth}px)`
                    };
                }
            },

            mounted() {
                this.updateContainerWidth();
                window.addEventListener('resize', this.updateContainerWidth);

                if (this.testimonials && this.testimonials.length > 1) {
                    this.startAutoPlay();
                }
            },

            beforeUnmount() {
                this.stopAutoPlay();
                window.removeEventListener('resize', this.updateContainerWidth);
            },

            methods: {
                updateContainerWidth() {
                    if (this.$refs.sliderContainer) {
                        this.containerWidth = this.$refs.sliderContainer.offsetWidth;
                    }
                },

                goTo(index) {
                    this.currentIndex = index;
                    this.resetAutoPlay();
                },

                next() {
                    if (this.currentIndex < this.testimonials.length - 1) {
                        this.currentIndex++;
                    } else {
                        this.currentIndex = 0;
                    }
                },

                startAutoPlay() {
                    this.autoPlayInterval = setInterval(() => {
                        this.next();
                    }, this.autoPlayDelay);
                },

                stopAutoPlay() {
                    if (this.autoPlayInterval) {
                        clearInterval(this.autoPlayInterval);
                        this.autoPlayInterval = null;
                    }
                },

                resetAutoPlay() {
                    this.stopAutoPlay();
                    this.startAutoPlay();
                },
            },
        });
    </script>
@endpushOnce
