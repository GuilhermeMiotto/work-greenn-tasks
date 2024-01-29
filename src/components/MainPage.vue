<template>
  <div class="main-container">
    <div class="navbar">
      <div class="left-section">
        <img class="logo" src="@/assets/sing.svg" alt="" />
        <span class="brand-name">TasksForGreenn</span>
      </div>
      <div class="right-section">
        <div class="tooltip-container">
          <i
            class="fas fa-plus navbar-icon"
            @click="openModal"
            @mouseover="showTooltip"
            @mouseout="hideTooltip"
            title="Criar nova tarefa"
          >
            <span class="tooltip">Criar nova tarefa</span>
          </i>
          <span class="tooltip" v-if="tooltipVisible">Criar nova tarefa</span>
        </div>
        <i class="fa-regular fa-circle-question navbar-icon"></i>
        <i class="fa-regular fa-bell navbar-icon"></i>
        <i class="fas fa-user-circle navbar-icon larger-icon"></i>
      </div>
    </div>

    <div class="main-content">
      <div class="left-rectangle">
        <div class="rectangle-item one" @click="fetchTasks">
          <i class="fas fa-inbox rectangle-icon"></i>
          <span class="rectangle-text">Entrada</span>
        </div>
        <div class="rectangle-item two" @click="fetchTasksToday">
          <i class="fas fa-calendar rectangle-icon"></i>
          <span class="rectangle-text">Tarefas de Hoje</span>
        </div>
        <div class="rectangle-item three" @click="fetchTasksOverdue">
          <i class="fas fa-triangle-exclamation rectangle-icon"></i>
          <span class="rectangle-text">Vencidos</span>
        </div>
      </div>

      <div class="right-rectangle">
        <span class="rectangle-text">Entrada</span>
      </div>

      <div class="container-tasks">
        <div class="tasks">
          <div class="view-task">
            <TaskDisplay
              v-if="showViewScreen"
              :task="selectedTask"
              @closeModal="closeViewScreen"
              @openViewScreen="openViewScreen"
              @delete-task="deleteTask"
            />
          </div>
          <TaskDisplay
            v-for="task in reversedTasks"
            :key="task.id"
            :task="task"
            @delete-task="deleteTask"
            @openViewScreen="openViewScreen"
            @closeModal="closeViewScreen"
          />
        </div>
      </div>
    </div>

    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <div class="modal-body">
          <div class="modal-input">
            <label for="taskName"></label>
            <input
              type="text"
              id="taskName"
              v-model="taskName"
              placeholder="Digite o nome da tarefa"
            />
          </div>
          <div class="modal-input">
            <label for="taskDescription"></label>
            <textarea
              id="taskDescription"
              v-model="taskDescription"
              placeholder="Digite a descrição da tarefa"
            ></textarea>
          </div>
          <div
            v-for="(subtask, index) in subtasks"
            :key="index"
            class="modal-input"
          >
            <label :for="'subtaskDescription' + index"></label>
            <textarea
              :id="'subtaskDescription' + index"
              v-model="subtask.description"
              placeholder="Digite a descrição da subtarefa"
            ></textarea>
          </div>
          <div v-if="showSubtaskInput" class="modal-input">
            <label for="subtaskDescription"></label>
            <textarea
              id="subtaskDescription"
              v-model="newSubtaskDescription"
              placeholder="Digite a descrição da subtarefa"
            ></textarea>
          </div>
          <div class="modal-input">
            <label for="taskDueDate">Data de Vencimento:</label>
            <div class="date-input-container">
              <i class="fa-regular fa-calendar-alt"></i>
              <input
                type="text"
                id="taskDueDate"
                v-model="taskDueDate"
                :class="{ 'valid-date': isDateValid, overdue: !isDateValid }"
                @input="handleDateInput"
                placeholder="DD/MM/AAAA"
              />
            </div>
          </div>
        </div>
        <hr class="modal-divider" />
        <div class="modal-footer">
          <button class="btn-cancel" @click="closeModal">Cancelar</button>
          <button
            class="btn-create-task"
            :class="{ disabled: !isDateValid }"
            @click="createTask"
          >
            Criar Tarefa
          </button>
        </div>
      </div>
    </div>
    <ViewScreen
      v-if="showViewScreen"
      :task="selectedTask"
      @closeModal="closeViewScreen"
      @openViewScreen="openViewScreen"
    />
  </div>
</template>

<script>
import axios from "axios";
import TaskDisplay from "./ModelTasks.vue";
import ViewScreen from "@/components/ViewTask.vue";

export default {
  components: {
    TaskDisplay,
    ViewScreen,
  },
  data() {
    return {
      showModal: false,
      taskName: "",
      taskDescription: "",
      taskDueDate: "",
      isDateValid: false,
      tooltipVisible: false,
      currentDate: "",
      subtasks: [],
      showSubtaskInput: false,
      newSubtaskDescription: "",
      taskDueDateTime: null,
      currentDateTime: null,
      tasks: [],
      selectedTask: null,
      showViewScreen: false,
    };
  },
  computed: {
    reversedTasks() {
      return this.tasks.slice().reverse();
    },
    isTaskOverdue() {
      return this.taskDueDateTime < this.currentDateTime;
    },
  },
  watch: {
    taskDueDate(newDate) {
      this.validateDate(newDate);
    },
    subtasks: {
      deep: true,
      handler() {
        this.validateDate(this.taskDueDate);
      },
    },
  },
  mounted() {
    this.fetchTasks();
  },
  methods: {
    openViewScreen(task) {
      console.log("Evento openViewScreen recebido em MainPage:", task);
      this.selectedTask = task;
      this.showViewScreen = true;
    },
    closeViewScreen() {
      this.showViewScreen = false;
      this.selectedTask = null;
    },

    deleteTask(task) {
      if (task && task.id) {
        const index = this.tasks.findIndex((t) => t.id === task.id);

        if (index !== -1) {
          this.tasks.splice(index, 1);
        } else {
          console.error("Tarefa não encontrada:", task);
        }
      } else {
        console.error(
          'Tarefa não está definida ou não tem uma propriedade "id".'
        );
      }
    },

    handleDeleteTask(taskToDelete) {
      const updatedTasks = this.tasks.filter(
        (task) => task.id !== taskToDelete.id
      );

      this.tasks = updatedTasks;
    },

    openModal() {
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.resetForm();
    },
    addSubtask() {
      if (this.showSubtaskInput && !this.isTaskOverdue) {
        const trimmedDescription = this.newSubtaskDescription.trim();
        if (trimmedDescription !== "") {
          this.subtasks.push({ description: trimmedDescription });
          this.newSubtaskDescription = "";
        }
      }
    },
    toggleSubtaskInput() {
      if (this.showSubtaskInput && this.newSubtaskDescription.trim() !== "") {
        this.subtasks.push({ description: this.newSubtaskDescription });
        this.newSubtaskDescription = "";
      } else {
        this.showSubtaskInput = true;
      }
    },
    validateDate(date) {
      const regexDate = /^\d{2}\/\d{2}\/\d{4}$/;
      const isDateValid = regexDate.test(date);

      if (isDateValid) {
        const [day, month, year] = date.split("/");
        const taskDate = new Date(`${year}-${month}-${day}`);
        const cutoffDate = new Date("2024-01-22");

        if (taskDate >= cutoffDate) {
          this.isDateValid = false;
        } else {
          this.isDateValid = true;
        }
      } else {
        this.isDateValid = true;
      }
    },
    handleDateInput(event) {
      const inputElement = event.target;
      const cursorPosition = inputElement.selectionStart;
      let formattedDate = event.target.value;

      const keyCode = event.keyCode || event.which;

      if (keyCode === 8) {
        if (
          (cursorPosition === 3 || cursorPosition === 6) &&
          formattedDate.charAt(cursorPosition - 1) === "/"
        ) {
          formattedDate =
            formattedDate.slice(0, cursorPosition - 1) +
            formattedDate.slice(cursorPosition);
        }
      } else if (keyCode === 37 || keyCode === 39) {
        //
      } else {
        if (
          (cursorPosition === 2 || cursorPosition === 5) &&
          formattedDate.charAt(cursorPosition - 1) !== "/"
        ) {
          formattedDate =
            formattedDate.slice(0, cursorPosition) +
            "/" +
            formattedDate.slice(cursorPosition);
        }
      }

      this.taskDueDate = formattedDate;

      if (!this.isDateValid) {
        inputElement.classList.add("valid-date");
        inputElement.classList.remove("overdue");
      } else {
        inputElement.classList.remove("valid-date");
        inputElement.classList.add("overdue");
      }
    },
    resetForm() {
      this.taskName = "";
      this.taskDescription = "";
      this.taskDueDate = "";
      this.isDateValid = false;
      this.subtasks = [];
      this.showSubtaskInput = false;
      this.newSubtaskDescription = "";
    },

    fetchTasks() {
      axios
        .get("http://localhost:8000/App/Api/api.php")
        .then((response) => {
          console.log(response.data);
          this.tasks = response.data.map((task) => ({
            ...task,
            data_vencimento: this.formatDate(task.data_vencimento),
          }));
        })
        .catch((error) => {
          console.error("Erro ao buscar tarefas:", error);
        });
    },

    fetchTasksToday() {
      axios
        .get("http://localhost:8000//actions/tarefas_hoje.php")
        .then((response) => {
          console.log(response.data);
          this.tasks = response.data.map((task) => ({
            ...task,
            data_vencimento: this.formatDate(task.data_vencimento),
          }));
        })
        .catch((error) => {
          console.error("Erro ao buscar tarefas do dia:", error);
        });
    },

    fetchTasksOverdue() {
      axios
        .get("http://localhost:8000/actions/vencidas.php")
        .then((response) => {
          console.log(response.data);
          this.tasks = response.data.map((task) => ({
            ...task,
            data_vencimento: this.formatDate(task.data_vencimento),
          }));
        })
        .catch((error) => {
          console.error("Erro ao buscar tarefas vencidas:", error);
        });
    },

    formatDate(date) {
      const options = { year: "numeric", month: "2-digit", day: "2-digit" };
      return new Date(date).toLocaleDateString("pt-BR", options);
    },

    createTask() {
      this.taskDueDateTime = new Date(this.taskDueDate);
      this.currentDateTime = new Date(this.currentDate);

      const formData = new FormData();

      formData.append("titulo", this.taskName);
      formData.append("descricao", this.taskDescription);
      formData.append("data_vencimento", this.taskDueDate);
      formData.append("status", "Pendente");

      axios
        .post("http://localhost:8000/actions/criar_tarefa.php", {
          titulo: this.taskName,
          descricao: this.taskDescription,
          data_vencimento: this.taskDueDate,
          status: "Pendente",
        })
        .then((response) => {
          console.log(response.data);
          this.fetchTasks();
        })
        .catch((error) => {
          console.error(error);
        });

      const newTask = {
        id: Date.now(),
        name: this.taskName,
        description: this.taskDescription,
        date: this.taskDueDate,
        completed: false,
        subtasks: this.subtasks,
      };

      this.tasks.unshift(newTask);

      this.resetForm();

      this.closeModal();
    },
    showTooltip() {
      this.tooltipVisible = true;
    },
    hideTooltip() {
      this.tooltipVisible = false;
    },
  },
};
</script>



<style scoped>
@import "~@fortawesome/fontawesome-free/css/all.css";

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Montserrat", sans-serif;
}

.main-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.navbar {
  background-color: black;
  height: 70px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
  color: white;
}

.fa-plus {
  cursor: pointer;
}

.left-section,
.right-section {
  display: flex;
  align-items: center;
}

.logo {
  height: 40px;
  width: 40px;
  margin-right: 3px;
}

.brand-name {
  font-size: 18px;
  font-weight: bold;
}

.navbar-icon {
  font-size: 16px;
  margin-left: 40px;
  font-family: "Font Awesome 5 Free";
  position: relative;
}

.navbar-icon:hover .tooltip,
.tooltip-container:hover .tooltip {
  display: inline-block;
}

.tooltip {
  display: none;
  position: absolute;
  background-color: black;
  color: white;
  padding: 5px;
  border-radius: 5px;
  margin-left: 10px;
  white-space: nowrap;
  top: 50%;
  right: calc(100% + 10px);
  transform: translateY(-50%);
}

.navbar-icon:hover .tooltip {
  display: inline-block;
}

.larger-icon {
  font-family: "Font Awesome 5 Free";
  font-size: 24px;
}

.main-content {
  display: flex;
}

.left-rectangle {
  width: 320px;
  height: 90vh;
  background-color: #f0f0f0;
  padding: 20px;
  margin-right: 20px;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
}

.rectangle-item {
  cursor: pointer;
  position: absolute;
}

.one {
  top: 149px;
  right: 88%;
}

.two {
  top: 219px;
  right: 83.85%;
}

.three {
  top: 289px;
  right: 87.4%;
}

.rectangle-icon {
  color: black;
  font-size: 24px;
  margin-right: 10px;
  font-family: "Font Awesome 5 Free";
}

.rectangle-text {
  text-align: center;
  font-size: 18px;
}

.right-rectangle {
  width: 701px;
  height: 129px;
  background-color: #ffffff;
  padding: 20px;
  flex: auto;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.right-rectangle span {
  font-weight: bold;
  font-size: 30px;
  margin-left: 165px;
  margin-bottom: -40px;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: #fff;
  width: 678px;
  max-width: 100%;
  border: 1px solid #ccc;
  border-radius: 8px;
  overflow: hidden;
}

.modal-body {
  padding: 10px;
}

.modal-input {
  margin-bottom: 10px;
}

.modal-input label {
  display: block;
  font-size: 14px;
  margin-bottom: 5px;
  text-align: left;
}

.date-input-container {
  left: 1.35%;
  width: 170px;
}

.modal-input input,
.modal-input textarea {
  width: calc(100% - 16px);
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 5px;
  height: 40px;
  justify-content: center;
}

.modal-input textarea {
  resize: vertical;
}

.date-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.modal-calendar-icon {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
}

.modal-divider {
  border: 0;
  height: 1px;
  background: #ccc;
  margin: 10px 0;
}

.modal-footer {
  padding: 10px;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.btn-cancel,
.btn-create-task {
  height: 40px;
  padding: 8px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.btn-cancel {
  background-color: rgb(212, 212, 212);
  color: #000000;
  margin-left: auto;
  margin-right: 10px;
}

.btn-create-task {
  background-color: #000000;
  color: #fff;
}

.btn-create-subtask {
  order: -1;
  margin-right: 10px;
}

.btn-create-subtask,
.btn-cancel,
.btn-create-task {
  width: auto;
}

.overdue {
  background-color: rgba(255, 0, 0, 0.1);
  border: 1px solid #ff0000;
  box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
}

.valid-date {
  background-color: rgba(0, 255, 0, 0.1);
  border: 1px solid #00ff00;
  box-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
}

.fa-calendar-alt {
  font-family: "Font Awesome 5 Free";
  position: absolute;
  left: 110px;
  top: 52%;
  transform: translateY(-50%);
}

.container-tasks {
  position: absolute;
  top: 26%;
  left: 34.39%;
  display: flex;
  width: 900px;
  height: 73vh;
  background-color: #ffffff;
  margin-right: 20px;
  overflow: auto;
  box-sizing: border-box;
  padding: 10px;
}


</style>