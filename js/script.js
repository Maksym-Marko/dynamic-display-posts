// recaptcha
// Vue.component( 'vue-recaptcha', VueRecaptcha )

// load more button
Vue.component( 'mx_ddp_load_more_button', {

	props: {
		ddpcount: {
			type: Number,
			required: true
		},
		ddpperpage: {
			type: Number,
			required: true
		},
		ddpcurrentpage: {
			type: Number,
			required: true
		},
		pageloading: {
			type: Boolean,
			required: true
		},
		load_img: {
			type: String,
			required: true
		},
		load_more_progress: {
			type: Boolean,
			required: true
		}
	},

	template: ` 
		<div v-if="!pageloading && ( ddpcurrentpage * ddpperpage ) < ddpcount">
			<p class="cmt-wrap-link text-center">
				<button
					v-if="!load_more_progress"
					type="button"
					class="btn cmt-button cmt-green cmt-shadow"
					@click.prevent="loadMore" 
				>Load More</button>
			</p>
			<div v-if="load_more_progress" class="mx-loading-ddp">
				<img :src="load_img" alt="" class="" />						
			</div>
		</div>
	`,
	data() {
		return {



		}
	},
	methods: {
		loadMore() {

			this.$emit( 'load_more', true )

		}
	}

} )

// search
Vue.component( 'mx_ddp_search', {

	props: {
		pageloading: {
			type: Boolean,
			required: true
		}
	},
	template: `
		<div class="mx-ddp-search">

			<form
				v-if="!pageloading"
				class="input-group mb-3"
			>
	            <div class="input-group-prepend">
	              <button
	              	class="input-group-text bg-white border-0"
	              	type='submit'
	              	@click.prevent="mxSearch"
	              	>
	                <i class='fas fa-search'></i>
	              </button>
	            </div>
	            <input
	            	type="text"
	            	class="form-control border-0"
	            	placeholder="Search title, research area, keywords, institution or associated projects…"
	              	v-model="search"
					@input="mxSearch"
	            >
	         </form>

		</div>
	`,
	data() {
		return {
			search: null,
			timeout: null
		}
	},
	methods: {
		mxSearch() {

			var _this = this

			clearTimeout( _this.timeout )

			let search_query = this.search

			if( search_query ) {

				if( search_query.length >= 3 ) {

					_this.timeout = setTimeout( function() {

						_this.$emit( 'mx-search-request', search_query )

					}, 1000 )

				}

			}

			if( !search_query ) {

				if( search_query !== null ) {

					_this.timeout = setTimeout( function() {

						_this.$emit( 'mx-search-request', search_query )

					}, 1000 )

				}

			}
			
		}
	}
} )

// item
Vue.component( 'mx_ddp_item', {

	props: {
		ddpitemdata: {
			type: Object,
			required: true
		}
	},

	template: `
	<div class="col-xl-4 col-lg-6 col-sm-6 col-12">
      <div class="card cmt-people-card cmt-shadow">
        <img :src="the_thumbnail" class="card-img-top" alt="...">
        <div class="card-body">
          <div class="card-text">
            <p class="card-text__name font-weight-light">
            	<a :href="the_permalink" class="mx-people-link">{{ the_title }}</a>
            </p>
            <p class="card-text__post">{{ post_excerpt }}</p>
          </div>
        </div>
      </div>
    </div>
	`,
	data() {
		return {
			no_user_phot: mxvjfepcdata_obj_front.no_user_phot
		}
	},
	computed: {
		the_id() {
			return this.ddpitemdata.ID
		},
		the_title() {
			return this.ddpitemdata.post_title
		},
		the_permalink() {

			return this.ddpitemdata.the_permalink

		},
		the_thumbnail() {

			let thumbnail = this.no_user_phot

			if( this.ddpitemdata.the_thumbnail ) {

				thumbnail = this.ddpitemdata.the_thumbnail

			}

			return thumbnail

		},
		post_excerpt() {

			return this.ddpitemdata.post_excerpt

		},
		the_content() {

			// line break
			var content = this.ddpitemdata.post_content

			content = content.replace(/\r?\n/g, '<br>')

			return content
		},
		the_answer() {

			var answer = this.ddpitemdata.answer

			answer = answer.replace(/\r?\n/g, '<br>')
			
			return answer
		},
		the_user_name() {
			return this.ddpitemdata.user_name
		},
		the_date() {
			let date = new Date( this.ddpitemdata.post_date )

			let day = date.getDate()

			let month = date.getMonth() + 1

			let year = date.getFullYear()

			return day + '/' + month + '/' + year
		},
	}
} )

// list of items
Vue.component( 'mx_ddp_list_items', {

	props: {
		getddpitems: {
			type: Array,
			required: true
		},
		parsejsonerror: {
			type: Boolean,
			required: true
		},
		pageloading: {
			type: Boolean,
			required: true
		},
		load_img: {
			type: String,
			required: true
		},
		no_items: {
			type: String,
			required: true
		},
	},
	template: `
		<div>

			<div v-if="parsejsonerror">
				${mxvjfepcdata_obj_front.texts.error_getting}
			</div>
			<div v-else>

				<div v-if="!getddpitems.length">				

					<div v-if="pageloading" class="mx-loading-ddp">
						<img :src="load_img" alt="" class="" />
					</div>
					<div v-else class="mx-no-items-found">
						{{ no_items }}
					</div>

				</div>
				<div 
					class="row"
					v-else
				>

					<mx_ddp_item
						v-for="item in get_items"
						:key="item.ID"				
						:ddpitemdata="item"
					></mx_ddp_item>

				</div>

			</div>				
			
		</div>
	`,
	data() {
		return {
		}
	},
	computed: {
		get_items() {
			return this.getddpitems
		}		
	}

} )

// ddp pagination
Vue.component( 'mx_ddp_pagination',	{

	props: {
		ddpcount: {
			type: Number,
			required: true
		},
		ddpperpage: {
			type: Number,
			required: true
		},
		ddpcurrentpage: {
			type: Number,
			required: true
		},
		pageloading: {
			type: Boolean,
			required: true
		}
	},

	template: `
		<div v-if="!pageloading && ( ddpcount - ddpperpage ) > 0">

			<ul class="mx-ddp-pagination">		

				<li
					v-for="page in coutPages"
					:key="page"
					:class="[page === ddpcurrentpage ? 'mx-current-page' : '']"
				><a 
					:href="'#page-' + page"
					@click.prevent="getPage(page)"
					>{{ page }}</a></li>

			</ul>

		</div>
	`,
	methods: {
		getPage( page ) {

			this.$emit( 'get-ddp-page', page )

		}
	},
	computed: {
		coutPages() {

			let difference = this.ddpcount / this.ddpperpage

			if( Number.isInteger( difference ) ) {
				return difference
			}

			return parseInt( difference ) + 1
		}
	}
} )

// form
Vue.component( 'mx_ddp_form',
	{
		template: `
			<div class="mx-iile-ddp-form-wrap" id="mx-iile-question-form">
				<div class="mx-form-title">
	                <span>${mxvjfepcdata_obj_front.texts.call_to_question}</span>
	            </div>

	            <form
					@submit.prevent="onSubmit"
					class="mx-iile-ddp-form"
					:class="[
						invalidForm ? 'mx_invalid_form' : '',
						messageSending ? 'mx_message_sending' : '',
						messageHasSent ? 'mx-message-has-sent' : ''
					]" 
				>
					<div class="mx-two-inputs-row">
						<div class="mx-form-control">
							<input
								type="text"
								placeholder="${mxvjfepcdata_obj_front.texts.p_your_name}"
								v-model="user_name"
								:class="{mx_empty_field: !user_name}"
							/>
							<small>${mxvjfepcdata_obj_front.texts.your_name}</small>
						</div>

						<div class="mx-form-control">
							<input
								type="email"
								placeholder="${mxvjfepcdata_obj_front.texts.p_your_email}"
								v-model="user_email"
								:class="[!user_email ? 'mx_empty_field' : '', !validateEmail( user_email ) ? 'mx_incorrect_email' : '']"
							/>
							<small>${mxvjfepcdata_obj_front.texts.your_email}</small>
							<small class="mx_small_inv_email">${mxvjfepcdata_obj_front.texts.your_email_failed}</small>
						</div>
					</div>

					<div class="mx-form-control">
						<input
							type="text"
							placeholder="${mxvjfepcdata_obj_front.texts.subject}"
							v-model="subject"
							:class="{mx_empty_field: !subject}"
						/>
						<small>${mxvjfepcdata_obj_front.texts.enter_subject}</small>
					</div>

					<div class="mx-form-control">
						<input
							type="checkbox"
							id="mx_agrement"
							v-model="agrement"
							:class="{mx_empty_field: !agrement}"
						/>
						<label for="mx_agrement">
							${mxvjfepcdata_obj_front.texts.agre_text} <a href="${mxvjfepcdata_obj_front.texts.agre_link}" target="_blank">${mxvjfepcdata_obj_front.texts.agre_doc_name}</a>
						</label>
						<small>${mxvjfepcdata_obj_front.texts.agre_failed}</small>
					</div>

					<div class="mx-form-control"> 
						<textarea
							cols="30" rows="10"
							placeholder="${mxvjfepcdata_obj_front.texts.your_message}"
							v-model="message"
							:class="{mx_empty_field: !message}"
						></textarea>
						<small>${mxvjfepcdata_obj_front.texts.your_message_failed}</small>
					</div>

					<!--<div class="mx-recaptcha-wrap">
						<vue-recaptcha sitekey="6Lfm3u8UAAAAAPmFbWF8HqhUi2Erc3p3luZoFpj4"
							@verify="getRecaptchaVerify"
							@expired="getRecaptchaExpired"
							:class="{mx_empty_field: !re_captcha}"></vue-recaptcha>
						<small>Проверка не пройдена</small>
					</div>-->			
				
					<div class="mx-send-message">
						<img :src="load_img" alt="" class="mx-sending-progress" />
						<button>${mxvjfepcdata_obj_front.texts.submit}</button>
					</div>
					
					<div class="mx-thank-you-for-message">
						<span>${mxvjfepcdata_obj_front.texts.success_sent}</span>
					</div>

				</form>
			</div>
		`,
		data() {
			return {
				user_name: null,
				user_email: null,
				agrement: false,
				subject: null,
				message: null,
				invalidForm: false,
				load_img: mxvjfepcdata_obj_front.loading_img,
				messageSending: false,
				messageHasSent: false
				//, re_captcha: null
			}
		},
		methods: {
			getRecaptchaVerify( response ) {

				this.re_captcha = response

			},
			// getRecaptchaExpired() {

			// 	this.re_captcha = null

			// 	console.log( 'expired' )

			// },
			onSubmit() {

				if( !this.messageHasSent ) {

					this.messageSending = true

					if(
						this.user_name &&
						this.user_email &&
						this.agrement &&
						this.subject &&
						this.message 
						//&& this.re_captcha
					) {

						// post
						var _this = this;

						var data = {

							action: 'mx_ddp_iile',
							nonce: mxvjfepcdata_obj_front.nonce,

							user_name: 	_this.user_name,
							user_email: _this.user_email,
							subject: 	_this.subject,
							message: 	_this.message,
							tax_query: _this.tax_query,
							post_type: _this.post_type
						};

						jQuery.post( mxvjfepcdata_obj_front.ajax_url, data, function( response ) {

							_this.sentDataReaction( response );							

						} );

					} else {

						this.invalidForm = true

						this.messageSending = false

					}
					this.validateEmail( this.user_email )

				}				

			},
			validateEmail( email ) {

			    let patern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
				
				return patern.test( String( email ).toLowerCase() )

			},
			sentDataReaction( response ) {

				if( response === 'integer' ) {

					this.user_name = null
					this.user_email = null
					this.agrement = false
					this.subject = null
					this.message = null

					this.messageHasSent = true

					this.messageSending = false

					this.invalidForm = false

				} else {

					this.messageSending = false
					
				}				

			}

		}
	}
)

if( document.getElementById( 'mx_ddp' ) ) {

	var app = new Vue( {
		el: '#mx_ddp',
		data: {
			noItemsMessages: {
				noItemsInDB: mxvjfepcdata_obj_front.texts.no_questions,
				noSearchItems: mxvjfepcdata_obj_front.texts.nothing_found
			},
			noItemsDisplay: '',
			ddpCurrentPage: 1,
			ddpPerPage: 2,
			ddpCount: 0,
			ddpItems: [],
			parseJSONerror: false,
			pageLoading: true,
			loadImg: mxvjfepcdata_obj_front.loading_img,
			query: '',
			tax_query: mx_ddp_tax_query,
			load_more_progress: false,
			post_type: mx_ddp_post_type
		},
		methods: {
			loadMoreItems() {

				this.load_more_progress = true

				let query = this.query

				let _this = this

				let data = {

					action: 'mx_ddp_load_more_items',
					nonce: mxvjfepcdata_obj_front.nonce,
					current_page: _this.ddpCurrentPage + 1,
					ddp_per_page: _this.ddpPerPage,
					query: query,
					tax_query: _this.tax_query,
					post_type: _this.post_type
				};			

				jQuery.post( mxvjfepcdata_obj_front.ajax_url, data, function( response ) {

					if( _this.isJSON( response ) ) {

						_this.get_count_ddp_items( query )

						let new_items = JSON.parse( response )

						_this.ddpItems = _this.ddpItems.concat( new_items );

						_this.ddpCurrentPage += 1

						_this.pageLoading = false

						_this.load_more_progress = false				

					} else {

						this.parseJSONerror = true

					}					

				} );				

			},
			searchQuestion( query ) {

				this.noItemsDisplay = this.noItemsMessages.noSearchItems

				// clear data ...
					this.ddpItems = []

					this.pageLoading = true

					let page = 1

					this.ddpCurrentPage = page

					this.ddpCurrentPage = page

					// history.pushState( { ddpPage: page },"",'#page-' + page )
				// ... clear data

				// set query
				let _query = ''

				if( query !== null ) {

					_query = query

				}

				this.query = _query

				var _this = this

				var data = {

					action: 'mx_search_ddp_items',
					nonce: mxvjfepcdata_obj_front.nonce,
					current_page: _this.ddpCurrentPage,
					ddp_per_page: _this.ddpPerPage,
					query: query,
					tax_query: _this.tax_query,
					post_type: _this.post_type
				};			

				jQuery.post( mxvjfepcdata_obj_front.ajax_url, data, function( response ) {

					if( _this.isJSON( response ) ) {

						_this.get_count_ddp_items( query )

						_this.ddpItems = JSON.parse( response );

						_this.pageLoading = false						

					} else {

						this.parseJSONerror = true

					}

				} );

			},
			changeddpPage( page ) {

				this.ddpCurrentPage = page

				// history.pushState( { ddpPage: page },"",'#page-' + page )

				this.get_ddp_items()
			},
			get_current_page() {

				let curretn_page = window.location.href

				if( curretn_page.indexOf( '#page-' ) >= 0 ) {

					let matches = curretn_page.match( /#page-(\d+)/ )

					let get_page = parseInt( matches[1] );

					if( Number.isInteger( get_page ) ) {

						this.ddpCurrentPage = get_page

					}		

				} else {

					// history.pushState( { ddpPage:'1' },"",'#page-1' )

				}				

			},
			get_count_ddp_items( query ) {

				let _query = ''

				if( query !== null ) _query = query

				var _this = this

				var data = {

					action: 'mx_get_count_ddp_items',
					nonce: mxvjfepcdata_obj_front.nonce,
					query: _query,
					tax_query: _this.tax_query,
					post_type: _this.post_type
				};				

				jQuery.post( mxvjfepcdata_obj_front.ajax_url, data, function( response ) {

					let count = parseInt( response )

					if( Number.isInteger( count ) )	{

						_this.ddpCount = count

					}

				} );

			},
			get_ddp_items() {

				this.noItemsDisplay = this.noItemsMessages.noItemsInDB

				var _this = this

				var data = {

					action: 'mx_get_ddp_items',
					nonce: mxvjfepcdata_obj_front.nonce,
					current_page: _this.ddpCurrentPage,
					ddp_per_page: _this.ddpPerPage,
					query: _this.query,
					tax_query: _this.tax_query,
					post_type: _this.post_type
				};			

				jQuery.post( mxvjfepcdata_obj_front.ajax_url, data, function( response ) {

					if( _this.isJSON( response ) ) {

						var result = JSON.parse( response );

						_this.ddpItems = result;

						_this.pageLoading = false

					} else {

						this.parseJSONerror = true

					}

				} );

			},
			isJSON( str ) {
				try {
			        JSON.parse(str);
			    } catch (e) {
			        return false;
			    }
			    return true;
			}
		},
		beforeMount() {

			// get current page
			this.get_current_page()

			// get count of ddp items
			this.get_count_ddp_items( null )

			// get ddp items
			this.get_ddp_items()
		}
	} )

}
