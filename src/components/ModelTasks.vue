<template>
  <div class="task-container" :style="computeTaskContainerStyle()">
    <div class="task-header">
      <div class="task-info">
        <input
          type="checkbox"
          class="checkbox"
          v-model="completed"
          @change="updateTaskState"
        />
        <div class="check-icon" v-if="completed">
          <i class="fas fa-check"></i>
        </div>
        <span class="task-name">{{ task.titulo }}</span>
        <div
          class="icon-container"
          @mouseover="showIcons"
          @mouseleave="hideIcons"
        >
          <i class="fas fa-pen" @click="openEditModal"></i>
          <i class="fa-solid fa-circle-info" @click="openViewScreen"></i>
          <i class="fas fa-trash" @click="deleteTask"></i>
        </div>

        <EditTasks
          :visible="isEditModalVisible"
          :task="task || {}"
          @close-modal="closeEditModal"
          @update-task="updateTask"
        />
      </div>
    </div>
    <div class="task-description">
      <p>{{ task.descricao }}</p>
    </div>
    <div class="task-footer">
      <span
        class="task-date"
        :class="{ overdue: !isDateValid, 'valid-date': isDateValid }"
      >
        <i class="fa-regular fa-calendar"></i> {{ task.data_vencimento }}
      </span>
    </div>
    <div v-if="task.subtasks && task.subtasks.length">
      <div v-for="(subtask, index) in task.subtasks" :key="index">
        {{ subtask.description }}
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import EditTasks from "@/components/EditTasks.vue";

export default {
  components: {
    EditTasks,
  },
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      completed: this.getCompletedState(),
      iconsVisible: false,
      isDateValid: true,
      isEditModalVisible: false,
      selectedTask: null,
      showViewScreen: false,
    };
  },
  computed: {
    isTaskOverdue() {
      const taskDueDate = new Date(this.task.date);
      const currentDate = new Date();
      return taskDueDate < currentDate;
    },
  },
  methods: {
    openViewScreen() {
      console.log("Evento openViewScreen emitido com task:", this.task);
      this.$emit("openViewScreen", this.task);
    },
    getCompletedState() {
      const storedState = localStorage.getItem(
        `task_completed_${this.task.id}`
      );
      return storedState ? JSON.parse(storedState) : this.task.completed;
    },

    updateCompletedState() {
      localStorage.setItem(
        `task_completed_${this.task.id}`,
        JSON.stringify(this.completed)
      );
    },

    updateTaskState() {
      axios
        .post(`http://localhost:8000/actions/marcar_conclusao_tarefa.php`, {
          id: this.task.id,
          completed: this.completed ? 1 : 0,
        })
        .then(() => {
          console.log("Estado da tarefa atualizado com sucesso.");
          this.updateCompletedState();
        })
        .catch((error) => {
          console.error("Erro ao atualizar estado da tarefa:", error);
        });
    },

    openEditModal() {
      this.isEditModalVisible = true;
      this.selectedTask = this.task;
    },

    closeEditModal() {
      this.isEditModalVisible = false;
    },

    updateTask(updatedTask) {
      if (this.task) {
        Object.assign(this.task, updatedTask);

        console.log("Tarefa atualizada no componente ModelTasks:", this.task);
        this.validateDate();
      } else {
        console.log("A tarefa não está definida ou é nula.");
      }

      this.closeEditModal();
    },

    editTask() {
      console.log("Editar Tarefa:", this.task);
    },
    editTaskDate() {
      console.log("Editar Data da Tarefa:", this.task);
    },
    deleteTask() {
      if (this.task && this.task.id) {
        axios
          .delete(
            `http://localhost:8000/actions/excluir_tarefa.php?id=${this.task.id}`
          )
          .then(() => {
            this.$emit("delete-task", this.task);
          })
          .catch((error) => {
            console.error("Erro ao excluir tarefa:", error);
          });
      } else {
        console.error(
          'Tarefa não está definida ou não tem uma propriedade "id".'
        );
      }
    },

    computeTaskContainerStyle() {
      return {
        width: "678px",
        height: "109px",
        padding: "15px",
        borderRadius: "10px",
        boxShadow: "0 0 10px rgba(0, 0, 0, 0.1)",
        margin: "10px",
        position: "relative",
      };
    },
    showIcons() {
      this.iconsVisible = true;
    },
    hideIcons() {
      if (!this.completed) {
        this.iconsVisible = false;
      }
    },

    validateDate() {
      const regexDate = /^\d{2}\/\d{2}\/\d{4}$/;
      this.isDateValid = regexDate.test(this.task.data_vencimento);

      if (this.isDateValid) {
        const [day, month, year] = this.task.data_vencimento.split("/");
        const taskDate = new Date(`${year}-${month}-${day}`);
        const cutoffDate = new Date("2024-01-22");

        this.isDateValid = taskDate >= cutoffDate;
      }
    },
  },

  created() {
    this.validateDate();
  },

  watch: {
    "task.date": "validateDate",
  },
};
</script>


<style scoped>
@import "~@fortawesome/fontawesome-free/css/all.css";

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.task-container {
  border: 1px solid #333;
}

.task-header {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.task-info {
  display: flex;
  align-items: center;
}

.checkbox {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  width: 20px;
  height: 20px;
  margin-right: 10px;
  border-radius: 50%;
  border: 2px solid #333;
  outline: none;
  cursor: pointer;
}

.checkbox:checked {
  background-color: #000000;
  border-color: #000000;
}

.check-icon {
  color: rgb(255, 255, 255);
  position: absolute;
  left: 0.7%;
  font-size: 12px;
  font-weight: bold;
  pointer-events: none;
}

.task-container:hover .icon-container {
  display: flex;
}

.task-name {
  position: absolute;
  font-weight: bold;
  font-size: 19px;
  left: 10%;
}

.task-description {
  word-wrap: break-word;
  max-height: 50px;
  max-width: 500px;
  margin-top: 5px;
  margin-bottom: 20px;
  position: absolute;
  left: 11.85%;
}

.icon-container {
  display: flex;
  position: absolute;
  top: calc(100% + 50%);
  right: 0;
  display: none;
  gap: 15px;
  color: gray;
  cursor: pointer;
  z-index: 1;
}

.task-footer {
  display: flex;
  justify-content: flex-end;
}

.overdue {
  color: #ff0000;
  background-color: rgba(255, 0, 0, 0.1);
  border: 1px solid #ff0000;
  box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
}

.valid-date {
  color: #009488;
  background-color: rgba(0, 255, 0, 0.1);
  border: 1px solid #00ff00;
  box-shadow: 0 0 5px #009488;
}
.task-date {
  width: 175px;
  position: absolute;
  top: 60%;
  left: 11.8%;
  border-radius: 5px;
  padding: 5px;
}

.fa-regular {
  position: absolute;
  top: 22%;
  left: 12%;
}

.fa-trash {
  color: rgb(249, 72, 72);
}
</style>
