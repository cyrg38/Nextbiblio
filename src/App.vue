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
				value="9782080811158" 
				@submit="searchNoticeFromIsbn(9782080811158)"/>
			<AppNavigationCounter :highlighted="true">{{ counter }}</AppNavigationCounter>
			<ul>
				<AppNavigationItem v-for="note in notes"
					:key="note.id"
					:title="note.title ? note.title : t('nextbiblio', 'New notice')"
					:class="{active: currentNoteId === note.id}"
					@click="openNotice(note)">
					<template slot="actions">
						<ActionButton v-if="note.id === -1"
							icon="icon-close"
							@click="cancelNewNotice(note)">
							{{ t('nextbiblio', 'Cancel notice creation') }}
						</ActionButton>
						<ActionButton v-else
							icon="icon-delete"
							@click="deleteNotice(note)">
							{{ t('nextbiblio', 'Delete notice') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
			<ul>
				<AppSettingsSection
					title="Settings for Nextbiblio"
					description="settings for calling API from user TOKEN on various ISBN DB"
					doc-url="">
					des trucs ici
				</AppSettingsSection>
			</ul>
		</AppNavigation>
		<AppContent>
			<div v-if="currentNote">
				Title : <input ref="title"
					v-model="currentNote.title"
					type="text"
					:disabled="updating" />
				ISBN : <input ref="isbn"
					v-model="currentNote.isbn"
					type="text"
					:disabled="updating" />
				uuid : <input ref="uuid"
					v-model="currentNote.uuid"
					type="text"
					:disabled="updating" />
				Authors : <input ref="authors"
					v-model="currentNote.authors"
					type="text"
					:disabled="updating" />
				Publishers : <input ref="publishers"
					v-model="currentNote.publishers"
					type="text"
					:disabled="updating" />
				Identifiers : <input ref="identifiers"
					v-model="currentNote.identifiers"
					type="text"
					:disabled="updating" />
				Tags : <input ref="tags"
					v-model="currentNote.tags"
					type="text"
					:disabled="updating" />
				Languages : <input ref="languages"
					v-model="currentNote.languages"
					type="text"
					:disabled="updating" />
				Emplacement : <input ref="emplacement"
					v-model="currentNote.emplacement"
					type="text"
					:disabled="updating"/>
				Cover : <img v-if="currentNote.cover" 
					ref="cover"
					:src="currentNote.cover"
					alt=""/>
					<img v-else
					src="https://via.placeholder.com/250x250"
					alt=""/><br/>
				TimeStamp : <DatetimePicker 
					type="datetime"
					ref="timestamp"
					v-model="currentNote.timestamp"
					:disabled="updating" />
				Pubdate : <DatetimePicker
					ref="pubdate"
					v-model="currentNote.pubdate"
					:disabled="updating" />
				Lu : <input type="checkbox"
					id="checkbox"
					ref="lu"
					:disabled="updating"
					v-model="currentNote.lu"/>
				<label for="checkbox">{{ checked }}</label>
				<textarea ref="comments" v-model="currentNote.comments" placeholder="add multiple lines" :disabled="updating" />
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
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
import AppNavigationCounter from '@nextcloud/vue/dist/Components/AppNavigationCounter'
import DatetimePicker from '@nextcloud/vue/dist/Components/DatetimePicker'
import AppSettingsSection from '@nextcloud/vue/dist/Components/AppSettingsSection'
import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
export default {
	name: 'App',
	components: {
		ActionButton,
		ActionInput,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		AppNavigationCounter,
		DatetimePicker,
		AppSettingsSection,
	},
	data() {
		return {
			notes: [],
			currentNoteId: null,
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected note object
		 * @returns {Object|null}
		 */
		currentNote() {
			if (this.currentNoteId === null) {
				return null
			}
			return this.notes.find((note) => note.id === this.currentNoteId)
		},
		/**
		 * Returns true if a note is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.title !== ''
		},
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/nextbiblio/notes'))
			this.counter = response.data.length-1
			this.notes = response.data
		} catch (e) {
			console.error(e)
			showError(t('nextbiblio', 'Could not fetch notices'))
		}
		this.loading = false
	},
	methods: {
		/**
		 * Create a new note and focus the note content field automatically
		 * @param {Object} note Note object
		 */
		openNotice(note) {
			if (this.updating) {
				return
			}
			this.currentNoteId = note.id
			this.$nextTick(() => {
				this.$refs.comments.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		saveNotice() {
			if (this.currentNoteId === -1) {
				this.createNote(this.currentNote)
			} else {
				this.updateNote(this.currentNote)
			}
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * The note is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNotice() {
			if (this.currentNoteId !== -1) {
				this.currentNoteId = -1
				this.notes.push({
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
		 * Search a new note from Databanks (in background from php)
		 */
		async searchNoticeFromIsbn(isbn) {
			console.log('a search again')
			const searchedNotice = await axios.get(generateUrl('/apps/nextbiblio/notes',isbn))
			console.log(isbn)
			console.log(searchedNotice)
			searchedNotice.id=-1
			this.notes.push(searchedNotice)
		},
		
		/**
		 * Abort creating a new note
		 */
		cancelNewNotice() {
			this.notes.splice(this.notes.findIndex((note) => note.id === -1), 1)
			this.currentNoteId = null
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createNote(note) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/nextbiblio/notes'), note)
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId)
				this.$set(this.notes, index, response.data)
				this.currentNoteId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('nextbiblio', 'Could not create the note'))
			}
			this.updating = false
		},
		/**
		 * Update an existing note on the server
		 * @param {Object} note Note object
		 */
		async updateNotice(note) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/nextbiblio/notes/${note.id}`), note)
			} catch (e) {
				console.error(e)
				showError(t('nextbiblio', 'Could not update the note'))
			}
			this.updating = false
		},
		/**
		 * Delete a note, remove it from the frontend and show a hint
		 * @param {Object} note Note object
		 */
		async deleteNotice(note) {
			try {
				await axios.delete(generateUrl(`/apps/nextbiblio/notes/${note.id}`))
				this.notes.splice(this.notes.indexOf(note), 1)
				if (this.currentNoteId === note.id) {
					this.currentNoteId = null
				}
				showSuccess(t('nextbiblio', 'Note deleted'))
			} catch (e) {
				console.error(e)
				showError(t('nextbiblio', 'Could not delete the note'))
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
		padding-left: 20px;
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
	
	textarea {
		flex-grow: 1;
		height: 10%;
		width: 100%;
	}
</style>

