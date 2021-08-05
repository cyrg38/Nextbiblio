<template>
	<div id="content" class="app-nextbiblio">
		<AppNavigation>
			<AppNavigationNew v-if="!loading"
				:text="t('nextbiblio', 'New notice')"
				:disabled="false"
				button-id="new-nextbiblio-button"
				button-class="icon-add"
				@click="newNotice" />
			<ActionInput icon="icon-edit"
				id="search-nextbiblio-isbn"
				class="search-nextbiblio-isbn"
				value="9782080811158"
				@submit="searchNoticeFromIsbn()"/>
			<ul>
				<AppNavigationItem v-for="author in authors"
					:key="author.author"
					:title="author.author"
					:class="{active: false}">
					<template slot="actions">
						<ActionInput
							icon="icon-search"
							class="author-search"
							value="search title"
							@click="searchNoticeFromTitle()"/>
						<ActionButton v-for="notice in author.notices"
							:key="notice.id"
							:title="notice.title ? notice.title : t('nextbiblio', 'New notice')"
							:class="{active: currentNoticeId === notice.id}"
							icon="{notice.lu ? 'icon-category-enabled':'icon-category-disabled'}"
							@click="openNotice(notice)">{{ notice.tags }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
			<AppNavigationSpacer/>
			<!--ul>
				<AppNavigationItem v-for="notice in notices"
					:key="notice.id"
					:title="notice.title ? notice.title : t('nextbiblio', 'New notice')"
					:class="{active: currentNoticeId === notice.id}"
					@click="openNotice(notice)">
					<template slot="actions">
						<ActionButton v-if="notice.id === -1"
							icon="icon-close"
							@click="cancelNewNotice(notice)">
							{{ t('nextbiblio', 'Cancel notice creation') }}
						</ActionButton>
						<ActionButton v-else
							icon="icon-delete"
							@click="deleteNotice(notice)">
							{{ t('nextbiblio', 'Delete notice') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul-->
			<AppNavigationSettings
				title="Settings for Nextbiblio"
				description="settings for calling API from user TOKEN on various ISBN DB"
				doc-url="">
			</AppNavigationSettings>
		</AppNavigation>
		<AppContent>
			<div v-if="currentNotice">
				Title : <input ref="title"
					v-model="currentNotice.title"
					type="text"
					:disabled="updating" />
				ISBN : <input ref="isbn"
					v-model="currentNotice.isbn"
					type="text"
					:disabled="updating" />
				uuid : <input ref="uuid"
					v-model="currentNotice.uuid"
					type="text"
					:disabled="updating" />
				Authors : <input ref="authors"
					v-model="currentNotice.authors"
					type="text"
					:disabled="updating" />
				Publishers : <input ref="publishers"
					v-model="currentNotice.publishers"
					type="text"
					:disabled="updating" />
				Identifiers : <input ref="identifiers"
					v-model="currentNotice.identifiers"
					type="text"
					:disabled="updating" />
				Tags : <input ref="tags"
					v-model="currentNotice.tags"
					type="text"
					:disabled="updating" />
				Languages : <input ref="languages"
					v-model="currentNotice.languages"
					type="text"
					:disabled="updating" />
				Emplacement : <input ref="emplacement"
					v-model="currentNotice.emplacement"
					type="text"
					:disabled="updating"/>
				Cover : <img v-if="currentNotice.cover" 
					ref="cover"
					:src="currentNotice.cover"
					alt=""/>
					<img v-else
					src="https://via.placeholder.com/250x250"
					alt=""/><br/>
				TimeStamp : <DatetimePicker 
					type="date"
					ref="timestamp"
					v-model="currentNotice.timestamp"
					:disabled="updating" />
				Pubdate : <DatetimePicker
					ref="pubdate"
					type="date"
					v-model="currentNotice.pubdate"
					:disabled="updating" />
				Lu : <input type="checkbox"
					id="checkbox"
					ref="lu"
					:disabled="updating"
					v-model="currentNotice.lu"/>
				<label for="checkbox">{{ checked }}</label>
				<textarea ref="comments" v-model="currentNotice.comments" placeholder="add multiple lines" :disabled="updating" />
				<input type="button"
					class="primary"
					:value="t('nextbiblio', 'Save')"
					:disabled="updating || !savePossible"
					@click="saveNotice"/>
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('nextbiblio', 'Create a notice to get started') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import DatetimePicker from '@nextcloud/vue/dist/Components/DatetimePicker'
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings'
import AppNavigationSpacer from '@nextcloud/vue/dist/Components/AppNavigationSpacer'
import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		DatetimePicker,
		AppNavigationSettings,
		ActionInput,
		AppNavigationSpacer,
	},
	data() {
		return {
			authors: [],
			notices: [],
			currentNoticeId: null,
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected notice object
		 * @returns {Object|null}
		 */
		currentNotice() {
			if (this.currentNoticeId === null) {
				return null
			}
			return this.notices.find((notice) => notice.id === this.currentNoticeId)
		},
		/**
		 * Returns true if a notice is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNotice && this.currentNotice.title !== ''
		},
	},
	/**
	 * Fetch list of notices when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/nextbiblio/notes'))
			this.counter = response.data.length-1
			this.notices = response.data
			
			var tmp = [];
			for (var i=0; i<this.notices.length; i++) {
				console.log(this.notices[i].timestamp)
				this.notices[i].timestamp = new Date(this.notices[i].timestamp)
				tmp.push(this.notices[i].authors)
			}
			this.authors = tmp.sort().filter((el,i,a) => (i===a.indexOf(el)))
			for (var i=0; i<this.authors.length; i++) {
				this.authors[i] = {"author" : this.authors[i], "notices" : []}
				for (var j=0; j<this.notices.length; j++) {
					if (this.authors[i].author == this.notices[j].authors) {
						this.authors[i].notices.push(this.notices[j])
					}
				}
			}
		} catch (e) {
			console.error(e)
			showError(t('nextbiblio', 'Could not fetch notices'))
		}
		this.loading = false
	},
	methods: {
		/**
		 * Create a new notice and focus the notice content field automatically
		 * @param {Object} notice Note object
		 */
		openNotice(notice) {
			if (this.updating) {
				return
			}
			this.currentNoticeId = notice.id
			this.$nextTick(() => {
				this.$refs.comments.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new notice or save
		 */
		saveNotice() {
			if (this.currentNoticeId === -1) {
				this.createNotice(this.currentNotice)
			} else {
				this.updateNotice(this.currentNotice)
			}
		},
		/**
		 * Create a new notice and focus the notice content field automatically
		 * The notice is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNotice() {
			if (this.currentNoticeId !== -1) {
				this.currentNoticeId = -1
				this.notices.push({
					id: -1,
					isbn: '',
					timestamp : (new Date()).toISOString()
					
				})
				this.$nextTick(() => {
					this.$refs.isbn.focus()
				})
			}
		},
		
		/**
		 * Search a new notice from isbn [we should create it if not already here]
		 */
		searchNoticeFromIsbn() {
			var isbn = $('.search-nextbiblio-isbn input[type="text"]')[0].value
			console.log(isbn)
			for (var i=0; i<this.notices.length; i++) {
				console.log(this.notices[i])
				if (this.notices[i].isbn == isbn) {
					this.openNotice(this.notices[i])
					return
				}
			}
			showError(t('nextbiblio', 'Isbn not found in Biblio'))
		},
		
		/**
		 * Search a new notice from isbn [we should create it if not already here]
		 */
		searchNoticeFromTitle(author) {
			var title = document.getElementById('author').value
			console.log(title)
			for (var i=0; i<this.notices.length; i++) {
				console.log(this.notices[i])
				if (this.notices[i].title.match(title)) {
					openNotice(this.notices[i])
					return
				}
			}
			showError(t('nextbiblio', 'Isbn not found in Biblio'))
		},
		
		/**
		 * Abort creating a new notice
		 */
		cancelNewNotice() {
			this.notices.splice(this.notices.findIndex((notice) => notice.id === -1), 1)
			this.currentNoticeId = null
		},
		/**
		 * Create a new notice by sending the information to the server
		 * @param {Object} notice Note object
		 */
		async createNotice(notice) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/nextbiblio/notes'), notice)
				const index = this.notices.findIndex((match) => match.id === this.currentNoticeId)
				this.$set(this.notices, index, response.data)
				this.currentNoticeId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('nextbiblio', 'Could not create the notice'))
			}
			this.updating = false
		},
		/**
		 * Update an existing notice on the server
		 * @param {Object} notice Note object
		 */
		async updateNotice(notice) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/nextbiblio/notes/${notice.id}`), notice)
			} catch (e) {
				console.error(e)
				showError(t('nextbiblio', 'Could not update the notice'))
			}
			this.updating = false
		},
		/**
		 * Delete a notice, remove it from the frontend and show a hint
		 * @param {Object} notice Note object
		 */
		async deleteNotice(notice) {
			try {
				await axios.delete(generateUrl(`/apps/nextbiblio/notes/${notice.id}`))
				this.notices.splice(this.notices.indexOf(notice), 1)
				if (this.currentNoticeId === notice.id) {
					this.currentNoticeId = null
				}
				showSuccess(t('nextbiblio', 'Note deleted'))
			} catch (e) {
				console.error(e)
				showError(t('nextbiblio', 'Could not delete the notice'))
			}
		},
	},
}
</script>
<style scoped>
	#app-content-vue > div {
		width: 90%;
		height: 100%;
		padding-top: 40px;
		padding-left: 30px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}
	
	#searchingIsbn {
		display: none;
	}
	
	input[type='text'] {
		width: 60%;
	}
	
	input[type='checkbox'] {
		min-height: 15px;
		width: max-content;
	}
	
	textarea {
		flex-grow: 1;
		height: 10%;
		width: 100%;
	}
</style>

