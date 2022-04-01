<template>
	<div class="dev">
		<b-col>
			<div class="dev__language">
				<h2>{{ $t('DEV.LANGUAGE') }}</h2>
			</div>
		</b-col>

		<b-col>
			<b-row>
				<b-col>
					<div :class="{ 'dev__btn-lang': true, 'dev__choose-lang': language === 'en' }">
						<b-button @click="setLanguage('en')">
							<b-img class="flag-icon" :src="English" />
							<span>{{ $t('DEV.ENGLISH') }}</span>
						</b-button>
					</div>
				</b-col>

				<b-col>
					<div :class="{ 'dev__btn-lang': true, 'dev__choose-lang': language === 'ja' }">
						<b-button @click="setLanguage('ja')">
							<b-img class="flag-icon" :src="Japan" />
							<span>{{ $t('DEV.JAPANESE') }}</span>
						</b-button>
					</div>
				</b-col>
			</b-row>
		</b-col>
	</div>
</template>

<script>
import Japan from '@/assets/images/japan.png';
import English from '@/assets/images/united-kingdom.png';
import { convertEraName } from '@/utils/convertEraName';

export default {
    name: 'PageDev',
    data() {
        return {
            Japan,
            English,

            convertEraName,

            year: '',
        };
    },
    computed: {
        language() {
            return this.$store.getters.language;
        },
    },
    methods: {
        setLanguage(lang) {
            this.$store.dispatch('app/setLanguage', lang)
                .then(() => {
                    this.$i18n.locale = lang;
                    this.$toast.success({
                        content: this.$t('TOAST.I18N.CHANGE_LANGUAGE.SUCCESS'),
                    });
                })
                .catch(() => {
                    this.$toast.error({
                        content: this.$t('TOAST.I18N.CHANGE_LANGUAGE.FAILED'),
                    });
                });
        },
    },
};
</script>

<style lang="scss" scoped>
    @import '@/scss/variables';

    .dev {
        &__language {
            text-align: center;
        }

        &__btn-lang {
            text-align: center;

            button {
                min-width: 150px;
                border: none;

                &:active {
                    background-color: $west-side !important;
                }
            }

            .flag-icon {
              max-width: 25px;
              max-height: 25px;
              vertical-align: middle;
            }
        }

        &__choose-lang {
            button {
                background-color: $west-side;
            }
        }
    }
</style>

