<script>
import simplebar from 'simplebar-vue';
import { SearchIcon, InfoIcon, MoreVerticalIcon } from '@zhuowenli/vue-feather-icons';
import { required, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import avatar2 from "@/assets/images/users/avatar-2.jpg";
import axios from 'axios'; // Import axios for API requests
import Swal from "sweetalert2";

export default {
  props: {
    conversations: Array,
    firebaseConfig: Object,
    selectedConversationId: Object,
  },
  setup(props) {
    const { t } = useI18n();
    const conversationsData = ref(props.conversations); // Make it a reactive reference
    const messagesData = ref([]); // New reactive reference for messages
    const form = ref({
      message: "",
    });
    const selectedConversationId = ref(props.selectedConversationId ?? null); // New reactive reference for the selected conversation ID
    const username = ref(""); // Reactive reference for username
    const profile = ref(avatar2); // Reactive reference for profile image
    const v$ = useVuelidate();

    const fetchMessages = async (conversationId) => {
      try {
        const response = await axios.get(`/chat/messages/${conversationId}`);
        messagesData.value = response.data;
        const conversation = conversationsData.value.find(conv => conv.id === conversationId);
        conversation.unread = 0;
        scrollToBottom('users-chat');
      } catch (error) {
        console.error('Error fetching messages:', error);
      }
    };

    const scrollToBottom = (id) => {
      setTimeout(() => {
        const simpleBar = document.getElementById(id).querySelector("#chat-conversation .simplebar-content-wrapper");
        const offsetHeight = document.getElementsByClassName("chat-conversation-list")[0].scrollHeight - window.innerHeight + 600;
        if (offsetHeight) {
          simpleBar.scrollTo({
            top: offsetHeight,
            behavior: "smooth",
          });
        }
      }, 300);
    };
    const isSubmitting = ref(false);

    const formSubmit = async (event) => {

      if (event) event.preventDefault();
      v$.value.$touch();
      if (v$.value.$invalid) return;

      if (isSubmitting.value) return;

      isSubmitting.value = true;

      const message = form.value.message;
      const currentDate = new Date();
      const hours = (currentDate.getHours() < 10 ? "0" : "") + currentDate.getHours();
      const minutes = (currentDate.getMinutes() < 10 ? "0" : "") + currentDate.getMinutes();
      
      const newMessage = {
        align: "right",
        message,
        time: `${hours}:${minutes}`,
        conversationId: selectedConversationId.value // Include the conversation ID for the new message
      };

      try {
        await axios.post('/chat/send-admin', newMessage); // Adjust API endpoint accordingly
        form.value.message = "";
        await fetchMessages(selectedConversationId.value); // Fetch updated messages after sending
        scrollToBottom('users-chat');
      } catch (error) {
        console.error('Error sending message:', error);
      }finally {
        isSubmitting.value = false;
      }
    };

    // Method to handle conversation selection
    const selectConversation = async (conversationId) => {
      selectedConversationId.value = conversationId;

      const selectedConversation = conversationsData.value.find(conv => conv.id === conversationId);
      console.log("selectedConversation", selectedConversation);

      if (selectedConversation) {
        // Update username and profile directly
        username.value = selectedConversation.subject;
        profile.value = selectedConversation.profile_picture;
      }

      await fetchMessages(conversationId); // Fetch messages for the selected conversation
    };

    const cancel = () => {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success me-2",
          cancelButton: "btn btn-danger ml-2",
        },
        buttonsStyling: false,
      });

      swalWithBootstrapButtons
        .fire({
          title: "Are you sure?",
          text: "You need to close this chat!",
          icon: "warning",
          confirmButtonText: "Yes, close it!",
          cancelButtonText: "No, cancel!",
          showCancelButton: true,
        })
        .then(async (result) => {
          if (result.isConfirmed) {
            try {
              // Axios call to close the chat
              await axios.post('/chat/close-chat', { conversationId: selectedConversationId.value }); // Adjust the endpoint and payload as needed
              // Optionally remove the conversation from the list or update UI state
              conversationsData.value = conversationsData.value.filter(conv => conv.id !== selectedConversationId.value);
              swalWithBootstrapButtons.fire("Closed!", "Your chat has been closed.", "success");
              selectedConversationId.value = null;
              messagesData.value = [];
            } catch (error) {
              console.error(error);
              swalWithBootstrapButtons.fire("Error!", "There was a problem closing the chat.", "error");
            }
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire("Cancelled", "Your chat conversation is safe :)", "error");
          }
        });
    };


const convertedTime = (time) => {
  if (time) {
    const date = new Date(time);
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
  }
  return ""; // Default value if time is invalid or empty
};

    const fetchUser = async (userId) => {
      try{
        const response = await axios.get(`/chat/fetch-user?user_id=${userId}`);
        return response.data;
      }catch(error) {
        console.error(error);
      }
    }
onMounted(async () => {
  try {
    if (selectedConversationId.value) {
      await selectConversation(selectedConversationId.value);
    }

    const firebaseConfig = props.firebaseConfig;
    if (!firebase.apps.length) {
      firebase.initializeApp(firebaseConfig);
    }

    const chatRef = firebase.database().ref('conversation');
    let timeout = false;

    // Format time for the existing conversationsData
    conversationsData.value.forEach((chat) => {
      chat.last_message_time = formatTime(new Date(chat.last_message_time)); // Format existing time
    });

    // Listen to changes in conversation messages
    conversationsData.value.forEach((chat) => {
      chatRef.child(chat.id).on('value', async function(snapshot) {
        const value = snapshot.val();

        if (timeout && value && value.sender_type !== 'user') {
          if (value.conversation_id == selectedConversationId.value) {
            const user = await fetchUser(value.sender_id);
            const timestamp = new Date(value.created_at);
            const formattedTime = formatTime(timestamp);

            let chatData = {
              id: value.message_id,
              align: 'left',
              message: value.message,
              profile_picture: profile.value,
              time: formattedTime, // Formatted time
            };
            messagesData.value.push(chatData);
            scrollToBottom('users-chat');
          } else {
            let selectedConversation = conversationsData.value.find(conv => conv.id === value.conversation_id);
            if (selectedConversation) {
              selectedConversation.unread = parseInt(selectedConversation.unread) + 1; // Update unread count
              selectedConversation.last_message_time = formatTime(new Date(value.created_at)); // Update last message time
            } else {
              console.error("Conversation not found!");
            }
          }
        }
      });
    });

    setTimeout(function () {
      timeout = true;
    }, 2000);

    chatRef.on('child_added', async function(snapshot) {
      if (timeout) {
        const value = snapshot.val();
        const user = await fetchUser(value.sender_id);

        const timestamp = new Date(value.created_at);
        const formattedTime = formatTime(timestamp);

        if (value.conversation_id == selectedConversationId.value) {
          let chatData = {
            id: value.message_id,
            align: 'left',
            message: user.name,
            profile_picture: user.profile_picture,
            time: formattedTime, // Use the formatted time
          };
          messagesData.value.push(chatData);
          scrollToBottom('users-chat');
        } else {
          let chatterData = {
            id: value.conversation_id,
            unread: 1,
            subject: user.name,
            profile_picture: user.profile_picture,
            last_message_time: formattedTime, // Use the formatted time for last message
          };
          conversationsData.value.unshift(chatterData);
        }
      }
    });

    // Function to format the date into "22 Nov 12:49 PM"
    function formatTime(date) {
      const options = {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      };
      return date.toLocaleString('en-GB', options).replace(',', ''); // Format like 22 Nov 12:49 PM
    }

  } catch (error) {
    console.error(error);
  }
});


    return {
      v$,
      form,
      conversationsData,
      messagesData,
      formSubmit,
      isSubmitting,
      selectedConversationId,
      scrollToBottom,
      convertedTime,
      selectConversation,
      username, // Return username
      profile, // Return profile
      cancel, // Return the cancel method
    };
  },

  components: {
    Layout,
    SearchIcon,
    InfoIcon,
    MoreVerticalIcon,
    simplebar,
    Head,
  },

  validations: {
    form: {
      message: {
        required: helpers.withMessage("Message is required", required),
      },
    },
  },

};
</script>


<template>
  
<Layout>
<div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
  <div class="chat-leftsidebar">
    <div class="px-4 pt-4 mb-4">
      <div class="d-flex align-items-start">
        <div class="flex-grow-1">
          <h5 class="mb-4">{{$t("chats")}}</h5>
        </div>
        <!-- <div class="flex-shrink-0">
          <div v-b-tooltip.hover title="Add Contact">
            <BButton type="button" variant="soft-success" size="sm">
              <i class="ri-add-line align-bottom"></i>
            </BButton>
          </div>
        </div> -->
      </div>
    </div>

    <simplebar class="chat-room-list" data-simplebar>
      <div class="d-flex align-items-center px-4 mb-2">
        <div class="flex-grow-1">
          <h4 class="mb-0 fs-11 text-muted text-uppercase">
            {{$t("direct_messages")}}
          </h4>
        </div>
      </div>

      <div class="chat-message-list">
        <SimpleBar class="list-unstyled chat-list chat-user-list">
          <li class v-for="data of conversationsData" :key="data.id" @click="selectConversation(data.id)"
          :class="{ active: selectedConversationId === data.id }" >
            <BLink href="javascript: void(0);">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 chat-user-img online align-self-center me-2 ms-0">
                  <div class="avatar-xxs avatar-xs" v-if="data.profile_picture">
                    <img :src="`${data.profile_picture}`" class="rounded-circle img-fluid userprofile avatar-xs" alt />
                  </div>
                  <div class="avatar-xxs avatar-xs" v-if="!data.profile_picture">
                    <div class="avatar-title rounded-circle bg-danger userprofile avatar-xs">
                      {{ data.profile_picture }}
                    </div>
                  </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <p class="text-truncate mb-1">
                    {{ data.subject }} 
                  </p>
                </div>
              <div class="d-flex align-items-center">
                <span v-if="data.unread > 0" class="position-absolute translate-middle badge border border-light rounded-circle bg-success p-1"> {{ data.unread }}</span>
              </div>

                <div class="flex-shrink-0">
                  <BBadge variant="dark-subtle" v-if="data.unread > 0" class="bg-dark-subtle text-body rounded p-1">{{ data.last_seen ?? convertedTime(data.last_message_time)
                  }}</BBadge>
                </div>
              </div>
            </BLink>
          </li>
        </SimpleBar>
      </div>

     
    </simplebar>
  </div>
  
  <div class="user-chat w-100 overflow-hidden">
    <div class="chat-content d-lg-flex">
      <div class="w-100 overflow-hidden position-relative">
        <div class="position-relative" v-if="selectedConversationId">
          <div class="p-3 user-chat-topbar">
            <BRow class="align-items-center">
              <BCol sm="4" cols="8">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 d-block d-lg-none me-3">
                    <BLink href="javascript: void(0);" class="user-chat-remove fs-18 p-1"><i
                        class="ri-arrow-left-s-line align-bottom"></i></BLink>
                  </div>
                  <div class="flex-grow-1 overflow-hidden" >
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                        <img :src="profile ? profile : require('@/assets/images/users/user-dummy-img.jpg')"
                          class="rounded-circle avatar-xs" alt="" />
                        <span class="user-status"></span>
                      </div>
                      <div class="flex-grow-1 overflow-hidden">
                        <h5 class="text-truncate mb-0 fs-16">
                          <BLink class="text-reset username">{{ username }}
                          </BLink>
                        </h5>
                      </div>
                    </div>
                  </div>
                </div>
              </BCol>
              <BCol sm="8" cols="4">
                <ul class="list-inline user-chat-nav text-end mb-0">

                  <li class="list-inline-item m-0">
                    <BDropdown variant="link" class="btn btn-ghost-secondary btn-icon" toggle-class=" arrow-none"
                      menu-class="dropdown-menu" aria-haspopup="true">
                      <template #button-content> <more-vertical-icon class="icon-sm"></more-vertical-icon>
                      </template>
                      <BDropdownItem><i class=" ri-mail-close-fill align-bottom text-danger me-2"></i>
                        <span class="text-danger" id="sa-params" @click="cancel">Close Chat</span></BDropdownItem>
                    </BDropdown>
                  </li>
                </ul>
              </BCol>
            </BRow>
          </div>
<!-- chat body -->
          <div class="position-relative" id="users-chat">
            <simplebar class="chat-conversation p-3 p-lg-4" id="chat-conversation" data-simplebar ref="current">
              <ul class="list-unstyled chat-conversation-list" v-if="messagesData.length > 0">
                  <li v-for="message in messagesData" :key="message.id" :class="['chat-list', message.align]">
                    <div class="conversation-list d-flex align-items-center">
                      <div class="chat-avatar" v-if="message.align !== 'right'">
                      <img :src="message.profile_picture" alt="" />                                          
                      </div>
                      <div class="user-chat-content">
                        <div class="ctext-wrap">
                          <div class="ctext-wrap-content">
                            <p class="mb-0 ctext-content">
                              {{ message.message }} 
                            </p>
                          </div>
                        </div>
                        <div class="conversation-name">
                          <small class="text-muted time">{{ message.time }}</small>
                          <span class="text-success">
                            <i class="bx bx-check"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>

            </simplebar>
          </div>
<!-- chat body end -->

<!-- chat input -->
          <div class="chat-input-section p-3 p-lg-4">
            <form @submit.prevent="formSubmit">
              <BRow class="g-0 align-items-center">
                <BCol>
                  <input :disabled="isSubmitting" v-model="form.message" :class="{ 'is-invalid': v$.form.message.$invalid }" 
                  type="text" class="form-control chat-input bg-light border-light"
                    :placeholder="$t('enter_message')" />
                    <!-- <div v-if="v$.form.message.$invalid" class="invalid-feedback">
                      {{ v$.form.message.$errors[0] }}
                    </div> -->
                </BCol>
                <BCol cols="auto">
                  <div class="chat-input-links ms-2">
                    <div class="links-list-item">
                      <BButton :disabled="isSubmitting" variant="success" type="submit" class="chat-send">
                        <i v-if="!isSubmitting"  class="ri-send-plane-2-fill align-bottom"></i>
                        <span v-else class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      </BButton>
                    </div>
                  </div>
                </BCol>
              </BRow>
            </form>
          </div>
<!-- chat input -->
        </div>
        <div class="position-relative" v-else>

        </div>
      </div>
    </div>
  </div>
</div>

</Layout>
 
</template>
